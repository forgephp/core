<?php

namespace Forge\Controller;

/**
 * Migrations
 *
 * A migration module inspired by Ruby on Rails
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Migrations extends Template
{
	public $template = 'migrations';

	protected $view;

	public function before() 
	{
		// Before anything, checks module installation
		$this->migrations = new Migrations( TRUE );

		$this->model = ORM::factory( 'Migration' );
		
		parent::before();
	}

	public function action_index() 
	{
		$migrations = $this->migrations->get_migrations();

		rsort( $migrations );

		//Get migrations already runned from the DB
		try 
		{
			$migrations_runned = ORM::factory( 'Migration' )
				->find_all()
				->as_array( 'hash' )
			;
		} 
		catch( Database_Exception $e ) 
		{
			$this->template->set_filename( 'migrations/install' );

			return;
		}


		$this->view = new View( 'migrations/index' );
		$this->view->set_global( 'migrations', $migrations );
		$this->view->set_global( 'migrations_runned', $migrations_runned );

		$this->template->view = $this->view;
	}

	public function action_new() 
	{
		$this->view = new View( 'migrations/new' );
		$this->template->view = $this->view;
	}

	public function action_create() 
	{
		$migration_name = str_replace( ' ', '_', $_REQUEST['migration_name'] );

		$session = Session::instance();
		
		try 
		{
      		if( empty( $migration_name ) )
      		{
      			throw new Foundation_Exception( 'Migration mame must not be empty' );
      		}

			$this->migrations->generate_migration( $migration_name );

			//Sets a status message
			$session->set( 'message', "Migration " . $migration_name . " was succefully created. Check migrations folder" );
	    } 
	    catch( Exception $e )
	    { 
			$session->set( 'message',  $e->getMessage() );
		}

	 	$this->redirect( '/migrations' );
	}

	public function action_migrate() 
	{
		$this->view = new View( 'migrations/migrate' );
		$this->template->view = $this->view;
		$this->view->set_global( 'messages', $this->migrations->migrate() );
	}

	public function action_rollback() 
	{
		$this->view = new View( 'migrations/rollback' );
		$this->template->view = $this->view;
		$this->view->set_global( 'messages', $this->migrations->rollback() );
	}
}
