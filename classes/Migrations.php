<?php

namespace Forge;

/** 
 * Flexible Migrations
 * 
 * An open source utility inspired by Ruby on Rails
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Migrations
{
	protected $_config;

	public function __construct()
	{
		$this->_config = Foundation::$config
			->load( 'migrations' )
			->as_array()
		;
	}

	public function get_config() 
	{
		return $this->_config;
	}

	/**
	 * Run all pending migrations
	 */
	public function migrate() 
	{
		$migration_keys = $this->get_migration_keys();

		$migrations = ORM::factory( 'migration' )
			->find_all()
		;

		$messages = array();

		//Remove executed migrations from queue
		foreach( $migrations as $migration ) 
		{
			if( array_key_exists( $migration->hash, $migration_keys ) )
			{
				unset( $migration_keys[$migration->hash] );
			}
		}

		if( count( $migration_keys ) > 0 )
		{
			foreach( $migration_keys as $key => $value )
			{
				$msg = "Executing migration: '" . $value . "' with hash: " . $key;
				
				try 
				{
					$migration_object = $this->load_migration($key);
					$migration_object->up();
					$model = ORM::factory('migration');
					$model->hash = $key;
					$model->name = $value;
					$model->save();
					$model ? $messages[] = array(0 => $msg) : $messages[] = array(1 => $msg);
				}
				catch (Exception $e)
				{
					$messages[] = array(1 => $msg . "\n" . $e->getMessage());
					return $messages;
				}
			}
		}
		return $messages;
	}

	/**
	 * Rollback last executed migration.
	 *
	 */
	public function rollback() 
	{
		//Get last executed migration
		$model = ORM::factory( 'migration' )
			->order_by( 'created_at', 'DESC' )
			->order_by( 'hash', 'DESC' )
			->limit( 1 )
			->find()
		;

		$messages = array();

		if( $model->loaded() )
		{
			try
			{
				$migration_object = $this->load_migration( $model->hash );
				$migration_object->down();

				if( $model )
				{
					$msg = "Migration '" . $model->name . "' with hash: " . $model->hash . ' was succefully "rollbacked"';
					$messages[] = array( 0 => $msg );
				}
				else
				{
					$messages[] = array( 1 => "Error executing rollback" );
				}

				$model->delete();
			}
			catch( Exception $e )
			{
				$messages[] = array( 1 => $e->getMessage() );
			}
		}
		else 
		{
			$messages[] = array( 1 => "There's no migration to rollback" );
		}

		return $messages;
	}

	/**
	 * Rollback last executed migration.
	 *
	 */
	public function get_timestamp() 
	{
		return date( 'YmdHis' );
	}

	/**
	 * Get all valid migrations file names
	 *
	 * @return array migrations_filenames
	 */
	public function get_migrations()	
	{
		$migrations = glob( $this->_config['path'] . '*' . EXT );

		foreach( $migrations as $i => $file )
		{
			$name = basename( $file, EXT );

			//Check filename format
			if( ! preg_match( '/^\d{14}_(\w+)$/', $name ) )
			{
				unset( $migrations[$i] );
			}
		}

		sort( $migrations );

		return $migrations;
	}

	/**
	 * Generates a new migration file
	 *
	 * @return integer completion_code
	 */
	public function generate_migration( $migration_name )
	{
		try
		{
			//Creates the migration file with the timestamp and the name from params
			$file_name = $this->get_timestamp() . '_' . $migration_name . '.php';
			$config = $this->get_config();
			$file = fopen( $config['path'] . $file_name, 'w+' );
			
			//Opens the template file and replaces the name
			$view = new View( 'migrations/template' );
			$view->set_global( 'migration_name', $migration_name );

			fwrite( $file, $view );
			fclose( $file );
			chmod( $config['path'] . $file_name, 0770 );

			return 0;
		}
		catch( Exception $e )
		{
			return 1;
		}
	}	

	/**
	 * Get all migration keys (timestamps)
	 *
	 * @return array migrations_keys
	 */
	protected function get_migration_keys() 
	{
		$migrations = $this->get_migrations();
		$keys = array();

		foreach( $migrations as $migration )
		{
			$sub_migration = substr( basename( $migration, EXT ), 0, 14 );

			$keys = Arr::merge( $keys, array( $sub_migration => substr( basename( $migration, EXT ), 15 ) ) );
		}

		return $keys;
	}

	/**
	 * Load the migration file, and returns a Migration object
	 *
	 * @return Migration object with up and down functions
	 */
	protected function load_migration( $version ) 
	{
		$f = glob( $this->_config['path'] . $version . '*' . EXT );

		// Only one migration per step is permitted
		if( count( $f ) > 1 )
		{
			throw new Foundation_Exception( 'There are repeated migration names' );
		}

		// Migration step not found
		if( count( $f ) == 0 )
		{
			throw new Foundation_Exception( 'There\'s no migration to rollback' );
		}

		$file = basename( $f[0] );
		$name = basename( $f[0], EXT );

		// Filename validation
		if( ! preg_match( '/^\d{14}_(\w+)$/', $name, $match ) )
		{
			throw new Foundation_Exception( 'Invalid filename :file', array( ':file' => $file ) );
		}

		$match[1] = strtolower( $match[1] );

		//Includes migration class file
		require $f[0];

		//Get the class name capitalized
		$class = ucfirst( $match[1] );

		if( ! class_exists( $class ) )
		{
			throw new Foundation_Exception( 'Class :class doesn\'t exists', array( ':class' => $class ) );
		}

		if( ! method_exists( $class, 'up' ) || ! method_exists( $class, 'down' ) )
		{
			throw new Foundation_Exception( 'Up or down functions missing on class :class', array( ':class' => $class ) );
		}

		return new $class();
	}
}
