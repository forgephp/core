<?php

namespace Forge\Task\Queue;

use Forge\Minion\Task;

/**
 * installs the queue database table
 *
 * Accepts the following options:
 *  - db: [default] 
 *
 *  Examples:
 *  $ ./minion queue:create
 *
 * @package    SuperFan
 * @category   Queue
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Create extends Task
{
    public function _execute( Array $params )    
    {
        $db = Database::instance();
        
        $table_name = 'Jobs';

        $sql = "CREATE TABLE IF NOT EXISTS " . $db->quote_table( $table_name ) . " (
            " . $db->quote_column( 'id' ) . " INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            " . $db->quote_column( 'handler' ) . " TEXT NOT NULL,
            " . $db->quote_column( 'queue' ) . " VARCHAR(255) NOT NULL DEFAULT 'default' ,
            " . $db->quote_column( 'attempts' ) . " INT(10) UNSIGNED NOT NULL DEFAULT '0',
            " . $db->quote_column( 'run_at' ) . " DATETIME DEFAULT NULL,
            " . $db->quote_column( 'locked_at' ) . " DATETIME DEFAULT NULL,
            " . $db->quote_column( 'locked_by' ) . " VARCHAR(255) DEFAULT NULL,
            " . $db->quote_column( 'failed_at' ) . " DATETIME DEFAULT NULL,
            " . $db->quote_column( 'error' ) . " TEXT,
            " . $db->quote_column( 'created_at' ) . " DATETIME NOT NULL,
            PRIMARY KEY (" . $db->quote_column( 'id' ) . ")
        )";

        Minion_CLI::write( 'Creating database table: ' . $db->quote_table( $table_name ) );

        $db->query( NULL, $sql );
    }
}