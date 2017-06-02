<?php

namespace Forge\Log;

/** 
 * Log DB
 * Database log writer. Stores log information in a database.
 * 
 * @package    SuperFan
 * @category   Log
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Database extends Writer
{	
	// Table name to write log data to
	protected $_table;

	// Creates a new file logger. Checks that the directory exists and
	// is writable.
	public function __construct( $table )
	{
		if( is_string( $table ) )
		{
			$this->_table = $table;
		}
	}

	// Writes each of the messages into the database table.
	public function write( array $messages)
	{
		foreach( $messages as $message )
		{
			// Write each message into the log database table
			DB::insert( $this->_table, array( 'time', 'level', 'body' ))
				->values(array(
					$message['time'],
					$message['level'],
					$message['body']
				)
			)
			->execute();
		}
	}

}
