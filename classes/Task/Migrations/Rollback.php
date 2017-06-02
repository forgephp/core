<?php

namespace Forge\Task\Migrations;

use Forge\Minion\Task;

/**
 * Minion task to rollback a migration
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Rollback extends Task
{
    /**
     * Task to rollback last executed migration
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
            Minion_CLI::write( 'Flexible Migrations is not installed. Please Run the migrations.sql script in your mysql server' );

            exit();
        }

        $messages = $migrations->rollback();

        if( empty( $messages ) )
        {
            Minion_CLI::write( "There's no migration to rollback" );
        }
        else
        {
            foreach( $messages as $message )
            {
                if( key( $message ) == 0 )
                {
                    Minion_CLI::write( $message[0] );
                }
                else
                {
                    Minion_CLI::write( $message[key($message)] );
                    Minion_CLI::write( "ERROR" );
                }
            }
        }
    }
}
