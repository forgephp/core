<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Minion task to install the migrations table
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Task_Migrations_Install extends Minion_Task
{
    protected $_options = array(
        'db' => Database::$default,
    );

    /**
     * @return null
     */
    protected function _execute( array $params )
    {
        $migrations = new Migrations( TRUE );

        if( ORM::factory( 'Migration' )->is_installed() )
        {
            Minion_CLI::write( 'The Migration database is already installed!' );

            exit();
        }
        
        //
    }
}
