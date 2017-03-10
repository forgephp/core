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
class Queue
{
    /**
     * Schedule a handler, or multiple handlers...
     *
     *
     * @param  Mixed  $handler
     * @param  String $queue
     * @throws ORM_Validation_Exception when the job isn't savable, logs it first, should be up to the application 
     *         to handle the exception, not queue library.
     * @return void
     */
    public static function schedule( $handler, $queue = 'default', $run_at = NULL )
    {
        // if handler is an array, then loop over each handler
        // and enqueue as normal 
        if( is_array( $handler ) )
        {
            foreach( $handler as $h )
            {
                self::schedule( $h, $queue, $run_at );
            }

            return;
        }

        try
        {
            ORM::factory( 'Job' )
                ->values(
                    array(
                        'handler' => $handler,
                        'run_at'  => self::parse_datetime($run_at), 
                        'queue'   => (string) $queue,
                    )
                )
                ->save()
            ;
        }
        catch( ORM_Validation_Exception $e )
        {
            Foundation::$log->add( Log::CRITICAL, '[JOB] failed to schedule job: ' . implode( ', ', $e->errors( 'Models' ) ) );
            throw $e;
        }

        return true;
    }

    /**
     * Parse given date, and return it in the correct format.
     *
     *
     * @param  Mixed $date
     * @return String
     */
    static public function parse_datetime( $date )
    {
        if( $date instanceof DateTime )
        {
            $date = $date->format( 'Y-m-d H:i:s' );

            return $date;
        }

        if( null === $date )
        {
            $date = new DateTime('now');

            return self::parse_datetime( $date );
        }

        $date = strtotime( $date );
        $date = date( 'Y-m-d H:i:s', $date );

        return $date;
    }
}
