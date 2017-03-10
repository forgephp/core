<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Queue
 *
 * @package    SuperFan
 * @category   Queue
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Model_Job extends ORM
{
    /**
     * Created at datetime
     *
     * @var Array
     */
    protected $_created_column = array(
        'column' => 'created_at',
        'format' => 'Y-m-d H:i:s',
    );

    /**
     * Auto-serialize/unserialize columns on get/set
     *
     * @var Array
     */
    protected $_serialize_columns = array(
        'handler',
    );

    /**
     * Validation Rules
     *
     *
     * @return Array
     */
    public function rules()
    {
        return array(
            'handler' => array(
                array( 'not_empty' ),
            ),
            'queue'   => array(
                array( 'not_empty' ),
            ),
        );
    }

    /**
     * Form Labels(?)
     *
     *
     * @return Array
     */ 
    public function labels()
    {
        return array(
            'handler' => 'Job Handler',
            'queue'   => 'Job Queue',
        );
    }

    protected function _serialize_value($value)
    {
        return serialize($value);
    }

    protected function _unserialize_value($value)
    {
        return unserialize($value);
    }

    /**
     * Find by worker name
     *
     *
     * @param  Mixed $worker
     * @return ORM
     */
    public function find_by_worker($worker)
    {
        if( $worker instanceof Queue_Worker )
        {
            $worker = $worker->name();
        }

        return $this->where( 'locked_by', '=', $worker );
    }

    /**
     * Find by Queue
     *
     *
     * @param  String $queue
     * @return ORM
     */
    public function find_by_queue($queue)
    {
        return $this->where( 'queue', '=', $queue );
    }

    /**
     * Find work for the handler
     * 
     * 
     * @param  Queue_Worker
     * @return Queue_Runner on success, false on no results being found
     */
    public function find_work( $worker )
    {
        // lock a job
        if( $this->lock( $worker ) )
        {
            $jobs = ORM::factory( 'Job' )
                ->find_by_queue( $worker->queue() )
                ->and_where_open()
                    ->where( 'run_at', 'IS', NULL )
                    ->or_where( DB::expr( 'UTC_TIMESTAMP()' ), '>=', DB::expr( 'run_at' ) )
                ->and_where_close()
                ->where( 'locked_by', '=', $worker->name() )
                ->where( 'failed_at', 'IS', NULL )
                ->where( 'attempts', '<', $worker->max_attempts() )
                ->order_by( 'created_at', 'DESC' )
                ->limit( 10 )
                ->find_all()
                ->as_array()
            ;
            
            foreach( $jobs as $job )
            {
                return new Queue_Runner( $worker, $job );
            }
        }
        
        return false;
    }
    
    /**
     * Lock the Job to the given $worker
     *
     *
     * @param  String $worker
     * @return Boolean
     */
    public function lock( $worker )
    {
        $lock = DB::select()
            ->from( 'Jobs' )
            ->where( 'locked_by', '=', $worker->name() )
            ->execute()
        ;
        
        if( $lock->count() >= 10 )
        {
            return true;
        }
        
        $jobs = DB::update( 'Jobs' )
            ->set( array( 'locked_by' => $worker->name(), 'locked_at' => DB::expr('CURRENT_TIMESTAMP') ) )
            ->where( 'locked_by', 'IS', NULL )
            ->limit( ( 10 - $lock->count() ) )
            ->execute()
        ;
        
        $total = $jobs + $lock->count();
        
        if( $total )
        {
            return true;
        }
        
        return false;
    }

    /**
     * Retry the job in X seconds
     *
     *
     * @param  Integer $delay
     * @return Boolean
     */
    public function retry($delay = 5)
    {
        return $this->values(
                array(
                    'run_at' => DB::expr(
                        'DATE_ADD(NOW), INTERVAL :delay SECOND',
                        array(
                            ':delay' => $delay
                        )
                    ),
                    'attempts' => DB::expr( 'attempts + 1' ),
                )
            )
        ->save()
        ;
    }

    /**
     * Release the job
     *
     *
     * @return Boolean
     */
    public function release()
    {
        return $this->values(
            array(
                'locked_at' => NULL,
                'locked_by' => NULL,
                )
            )
            ->save()
        ;
    }

    /**
     * Invoke handler
     *
     * 
     * @return void
     */
    public function invoke()
    {
        try
        {
            $this->hook( 'before', $this );

            $this->payload()->perform();

            $this->hook( 'success', $this );
        }
        catch( Exception $e )
        {
            $this->hook( 'error', $e );
        }

        $this->hook( 'after', $this );

        // well, cause PHP version < 5.5 does not support finally :/
        if( isset( $e ) && $e instanceof Exception )
        {
            throw $e;
        }
    }

    /**
     * Check the handler to see if its an object,
     * otherwise throw an exception
     *
     *
     * @throws Queue_Exception_Invalid_Payload if handler isn't an object
     * @return Object
     */
    protected function payload()
    {
        $handler = $this->__get( 'handler' );
        if( isset( $handler ) && is_object( $handler ) )
        {
            return $handler;
        }

        throw new InvalidArgumentException(
            __(
                "Invalid handler for job::id",
                array(
                    ':id' => $this->pk(),
                )
            )
        );
    }

    /** 
     * Invoke Hook on handler
     *
     *
     * @param  String $method
     * @param  Mixed  $arg0
     * @param  Mixed  $argN
     * @return Mixed
     */
    public function hook()
    {
        $args = func_get_args();
        $meth = array_shift( $args );

        try
        {
            if( method_exists( $this->payload(), $meth ) ) 
            {
                return $this->payload()->$meth($args);
            }
        }
        catch( InvalidArgumentException $e )
        {
            // do nothing in this instance...
            return null;
        }
    }

    /**
     * Release all jobs for this worker
     *
     *
     * @param  Mixed $worker
     * @return void
     */
    public function release_all($worker)
    {
        foreach( $this->find_by_worker($worker)->find_all() as $job )
        {
            $job->release();
        }
    }

    /**
     * Mark the job as failed
     *
     *
     * @return ORM
     */
    public function fail()
    {
        $this->values(
            array(
               'failed_at' => DB::expr('CURRENT_TIMESTAMP'),
            )
        );

        return $this->save();
    }

    /**
     * Set the error message
     *
     * Example:
     *
     *    $job->fail()->with_error( implode(', ', $e->errors( 'Models' ) ) )->save();
     *
     * @param  Mixed $message
     * @return ORM
     */
    public function with_error($message, Array $args = array())
    {
        return $this->values(
            array(
                'error' => (string) __($message, $args),
            )
        );
    }

    /**
     * Finish, and delete itself
     *
     *
     * @return ORM
     */
    public function finish()
    {
        return $this->delete();
    }

    public function max_attempts()
    {
        try
        {
            return method_exists( $this->payload(), 'max_attempts' ) ? $this->payload()->max_attempts() : null;    
        }
        catch( InvalidArgumentException $e )
        {
            return null;
        }
        
    }

    /**
     * Time to reschedule at
     *
     * 
     * @return String
     */
    public function reschedule_at()
    {
        try
        {
            if( method_exists( $this->payload(), 'reschedule_at' ) )
            {
                $t = $this->payload()->reschedule_at( date( 'Y-m-d h:i:s' ), $this->attempts );
            }
            else
            {
                $t = time() + ($this->attempts ^ 4) + 5;    
            }
        }
        catch( InvalidArgumentException $e )
        {
            $t = time() + ($this->attempts ^ 4) + 5;
        }
        
        return date( 'Y-m-d h:i:s', $t );
    }

    /**
     * Requeue job
     *
     *
     * @return ORM
     */ 
    public function requeue()
    {
        return $this->values(
            array(
                'failed_at' => NULL,
                'run_at'    => DB::expr('CURRENT_TIMESTAMP'),
                'attempts'  => 0,
                'locked_at' => NULL,
                'locked_by' => NULL,
            )
        )->save();
    }
}
