<?php

namespace Forge\Model;

use Forge\Model;

/** 
 * Database Model base class.
 * 
 * @package    SuperFan
 * @category   Log
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
abstract class Database extends Model
{
	/**
	 * Create a new model instance. A [Database] instance or configuration
	 * group name can be passed to the model. If no database is defined, the
	 * "default" database group will be used.
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param   string   $name  model name
	 * @param   mixed    $db    Database instance object or string
	 * @return  Model
	 */
	public static function factory( $name, $db = NULL )
	{
		// Add the model prefix
		$class = 'Model_' . $name;

		return new $class( $db );
	}

	// Database instance
	protected $_db;

	/**
	 * Loads the database.
	 *
	 *     $model = new Foo_Model($db);
	 *
	 * @param   mixed  $db  Database instance object or string
	 * @return  void
	 */
	public function __construct( $db = NULL )
	{
		if( $db )
		{
			// Set the instance or name
			$this->_db = $db;
		}
		else if( ! $this->_db )
		{
			// Use the default name
			$this->_db = Database::$default;
		}

		if( is_string( $this->_db ) )
		{
			// Load the database
			$this->_db = Database::instance( $this->_db );
		}
	}
}
