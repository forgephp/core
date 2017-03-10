<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Queue
 *
 * @package    SuperFan
 * @category   Queue
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Queue_Runner
{
	/**
     * Worker
     *
     *
     * @var Queue_Worker
     */
    protected $worker;

    /**
     * Job Model
     *
     *
     * @var Model_Job
     */
    protected $job;

    /**
     * @param  Queue_Worker $worker
     * @param  Model_Job    $job
     * @return Queue_Handler
     */
    public function __construct( Queue_Worker $worker, Model_Job $job )
    {
        $this->worker = $worker;

        $this->job    = $job;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        unset( $this->job );
        unset( $this->worker ); // this could be bad o_O
    }

    /**
     * Perform Job
     *
     * @return void
     */
    public function run()
    {
        $job = $this->job();

        try
        {
            $job->invoke();

            $job->finish();
        }

        catch( Queue_Runner_Exception_Retry $e )
        {
            $this->log(
                Log::ERROR,
                '[ERROR] Runner::run() failed with message: :message',
                array( ':message' => $e->getMessage() )
            );
            
            $this->reschedule( $job );
        }
        catch( Queue_Runner_Exception $e )
        {
            $job->error = $e->getMessage() . "\n" . $e->getTraceAsString();

            $this->failed( $job );
        }
        catch( Exception $e )
        {
            $this->handle_failed_job( $job, $e );
        }

        unset( $job );
    }

    /**
     * The Job
     *
     * @return Model_Job
     */
    public function job()
    {
        return $this->job;
    }

    /**
     * Log Message
     *
     * @param  Integer $lvl
     * @param  String  $msg
     * @param  Array   $args
     * @return void
     */
    public function log()
    {
        $args = func_get_args();
        return call_user_func_array( array($this->worker, 'log'), $args );
    }

    /**
     * Amount of time to sleep between jobs
     *
     * @return Integer
     */
    public function sleep()
    {
        return $this->worker->sleep();
    }

    /**
     * Job Count
     *
     * @return Integer
     */
    public function count()
    {
        return $this->worker->count();
    }

    /**
     * Queue Name
     *
     * @return String
     */
    public function queue()
    {
        return $this->worker->queue();
    }

    /**
     * Worker Name
     *
     * @return String
     */
    public function name()
    {
        return $this->worker->name();
    }

    /**
     * Max Attempts
     *
     * @param  Model_Job $job
     * @return Integer
     */
    public function max_attempts( Model_Job $job )
    {
        return is_null( $job->max_attempts() ) ? $this->worker->max_attempts() : $job->max_attempts();
    }

    /**
     * PID
     *
     * @return Mixed
     */ 
    public function pid()
    {
        return $this->worker->pid();
    }

    /** 
     * run failure hook, and fail job
     *
     * @param  Model_Job $job
     * @return ORM
     */
    public function failed( Model_Job $job )
    {
        $job->release();
        
        return $job->fail();
    }

    /**
     * Reschedule
     *
     * @param  Model_Job $job
     * @return Mixed
     */
    public function reschedule( $job )
    {
        if( $job->attempts < $this->max_attempts( $job ) )
        {
            $job->run_at = $job->reschedule_at();
            
            return $job->release();
        }

        return null;
    }

    /**
     * Handle the failed job
     *
     *
     * @param  Model_Job $job
     * @param  Exception $exception
     * @return Mixed
     */
    public function handle_failed_job( Model_Job $job, Exception $exception )
    {
        $job->attempts += 1;
        $job->save();

        $this->log( Log::ERROR, '[JOB] FAILED (:attempts prior attempts) with :class: :message',
            array(
                ':attempts' => $job->attempts,
                ':class'    => get_class( $exception ),
                ':message'  => $exception->getMessage(),
            )
        );

        if( null !== $this->reschedule( $job ) )
        {
            return;
        }

        $job->with_error( $exception->getMessage() . "\n" . $exception->getTraceAsString() );

        $this->log( Log::ERROR, '[JOB] REMOVED job::id because of :attempts consecutive failures', 
            array(
                ':id' => $job->pk(),
                ':attempts' => $job->attempts,
            )
        );

        return $this->failed( $job );
    }
}
