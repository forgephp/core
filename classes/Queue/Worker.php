<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Queue Worker
 *
 * @package    SuperFan
 * @category   Queue
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Queue_Worker
{
    // Defaults (Constants)
    const DEFAULT_QUEUE 	   = 'default';
    const DEFAULT_COUNT 	   = 0;
    const DEFAULT_SLEEP        = 5;
    const DEFAULT_MAX_ATTEMPTS = 5;
    const DEFAULT_VERBOSE 	   = TRUE;
    const DEFAULT_DEBUG        = FALSE;

    /**
     * Default Worker Options
     *
     * @var Array
     */
    protected static $defaults = array(
        'queue'         => self::DEFAULT_QUEUE,
        'count'         => self::DEFAULT_COUNT,
        'sleep'         => self::DEFAULT_SLEEP,
        'max_attempts'  => self::DEFAULT_MAX_ATTEMPTS,
        'verbose'       => self::DEFAULT_VERBOSE,
        'debug'         => self::DEFAULT_DEBUG,
    );

    /**
     * Worker Options
     *
     * @var Array
     */
    protected $options = array();

    /**
     * Worker Constructor
     *
     * @param  Array $options
     * @return Queue_Worker
     */
    public function __construct( $options=array() )
    {
        $this->merge( $options );

        $this->setup();
    }

    /**
     * Start process...
     *
     * @return void
     */
    public function start()
    {
        $this->log( Log::INFO, '[JOB] Starting worker :name on queue::queue',
            array(
                ':name'  => $this->name(),
                ':queue' => $this->queue(),
            )
        );
        
        $count = $job_count = 0;
        
        while( $this->count() === 0 || $count < $this->count() )
        {
            if( function_exists( 'pcntl_signal_dispatch' ) ) pcntl_signal_dispatch();
            
            $this->log( Log::DEBUG, '[DEBUG] :memory bytes currently in use', 
                array(
                    ':memory' => memory_get_usage( true )
                )
            );
            
            $runner = ORM::factory( 'Job' )
                ->find_work( $this )
            ;
            
            $count += 1;
            
            if( ! $runner )
            {
                $this->log( Log::DEBUG, '[JOB] Failed to get a job, queue::queue may be empty', array( ':queue' => $this->queue() ) );
                
                // unset the runner here if no jobs are found to prevent bloat
                unset( $runner );
                
                usleep( $this->sleep() );
                
                continue;
            }
            
            try
            {
                $runner->run();
                
                $job_count += 1;
            }
            catch( Exception $e )
            {
                $this->log( Log::ERROR, '[JOB] unhandled :class exception: :msg', 
                    array(
                        ':class' => get_class( $e ),
                        ':msg'   => $e->getMessage() . "\n" . $e->getTraceAsString(),
                    ) 
                );
            }
            
            unset( $runner );
            
            usleep( $this->sleep() );
        }
        
        $this->log( Log::INFO, '[JOB] worker shutting down after running :job_count jobs over :count polling iterations',
            array(
                ':count'     => $count,
                ':job_count' => $job_count,
            )
        );
    }

    /**
     * Handle shutting down the worker..
     *
     * @param  Integer $signo
     * @return void
     */
    public function shutdown( $signo )
    {
        $signals = array(
            SIGTERM => 'SIGTERM',
            SIGINT  => 'SIGINT',
        );

        ORM::factory( 'Job' )
            ->release_all( $this->name() )
        ;
        
        $this->log( Log::INFO, '[WORKER] Received :signal... shutting down',
            array(
                ':signal' => $signals[$signo],
            )
        );
        
        exit( 0 );
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
        $lvl  = array_shift( $args );
        $msg  = array_shift( $args );
        $args = array_shift( $args );

        if( ! empty( $args ) )
        {
            $msg = __( $msg, $args );
        }

        // skip doing anything if debugging isn't on but the lvl is set to debug
        if( ! $this->is_debug() && $lvl === Log::DEBUG ) return;

        Foundation::$log->add( $lvl, $msg );
        Foundation::$log->write();

        if( $this->is_verbose() )
        {
            Minion_CLI::write( sprintf("[%s] %s", date( 'c' ), $msg) );
        }
    }

    /**
     * Merge, and cleanup options for the worker
     *
     * @param  Array $options
     * @return void
     */
    protected function merge( Array $options )
    {
        foreach( $options as $key => $value )
        {
            // unset invalid configs
            if( ! isset( self::$defaults[$key] ) )
            {
                unset( $options[$key] );
                
                continue;
            }
        }

        $this->options = $options;
    }

    /**
     * Setup the worker
     *
     * @return void
     */
    protected function setup()
    {
        $this->options['name'] = 'host::' . trim( `hostname` );

        if( function_exists( 'pcntl_signal' ) )
        {
            pcntl_signal( SIGTERM, array( $this, 'shutdown' ) );
            pcntl_signal( SIGTERM, array( $this, 'shutdown' ) );
        }
    }

    /**
     * Should we be verbose?
     *
     * @return Boolean
     */
    public function is_verbose()
    {
        return FALSE !== $this->options['verbose'];
    }

    /**
     * Should we spit out debug messages?
     *
     * @return Boolean
     */
    public function is_debug()
    {
        return FALSE !== $this->options['debug'];
    }

    /**
     * Amount of time to sleep between jobs
     *
     * @return Integer
     */
    public function sleep()
    {
        return $this->options['sleep'] * 1000;
    }

    /**
     * Job Count
     *
     * @return Integer
     */
    public function count()
    {
        return $this->options['count'];
    }

    /**
     * Queue Name
     *
     * @return String
     */
    public function queue()
    {
        return $this->options['queue'];
    }

    /**
     * Worker Name
     *
     * @return String
     */
    public function name()
    {
        return $this->options['name'];
    }

    /**
     * Max Attempts
     *
     * @return Integer
     */
    public function max_attempts()
    {
        return $this->options['max_attempts'];
    }
}
