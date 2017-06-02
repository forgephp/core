<?php

namespace Forge\Task\Migrations;

use Forge\Minion\Task;

/**
 * Minion task to run migrations
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Run extends Task
{
    /**
     * Task to run pending migrations
     *
     * @return null
     */
    protected function _execute( array $params )
    {
        $migrations = new Migrations( TRUE );

        try 
        {
            ORM::factory( 'Migration' )->is_installed();
        } 
        catch( Database_Exception $a )
        {
            Minion_CLI::write( 'The Migrations table is not installed. Please run $ ./minion migration:install on your server' );

            exit();
        }

        $messages = $migrations->migrate();

        if( empty( $messages ) )
        {
            Minion_CLI::write( "Nothing to migrate" );
        }
        else
        {
            foreach( $messages as $message )
            {
                if( key( $message ) == 0 )
                {
                    Minion_CLI::write( $message[0] );
                    Minion_CLI::write( "OK" );
                }
                else
                { 
                    Minion_CLI::write( $message[key( $message )] );
                    Minion_CLI::write( "ERROR" );
                }
            }
        }
    }
}
