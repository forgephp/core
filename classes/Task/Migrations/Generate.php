<?php

namespace Forge\Task\Migrations;

use Forge\Minion\Task;

/**
 * Minion task to generate a migration
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Generate extends Task
{
    protected $_options = array(
        'name' => NULL,
    );

    public function build_validation( Validation $validation )
    {
        return parent::build_validation( $validation )
            ->rule( 'name', 'not_empty' )
        ;
    }

    /**
     * Task to build a new migration file
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

        $status = $migrations->generate_migration( $params['name'] );

        if( $status == 0 )
        { 
            Minion_CLI::write( 'Migration ' . $params['name'] . ' was succefully created' );
            Minion_CLI::write( 'Please check migrations folder' );
        } 
        else 
        {
            Minion_CLI::write( 'There was an error while creating migration ' . $params['name'] );
        }
    }
}
