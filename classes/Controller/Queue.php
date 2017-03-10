<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Queue
 *
 * A task manager inpired by Ruby on Rails' Disk Jockey
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Controller_Queue extends Controller_Template
{
	protected $job;

	public $template = 'queue/index';

	public function before()
	{
		parent::before();

		if( false !== ( $id = $this->request->param( 'id' ) ) )
		{
			$this->job = ORM::factory( 'Job', $id );
		}

		View::set_global(
			array(
				'active' => $this->request->action(),
				'tabs'   => array(
					'Queued'  => '/queue/queued',
					'Working' => '/queue/working',
					'Pending' => '/queue/pending',
					'Failed'  => '/queue/failed',
				)
			)
		);
	}

	public function action_index()
	{
		$this->template->set_filename( 'queue/index' );

		$this->template->set(
			array(
				'queued_count'  => $this->jobs( 'queued' )->count_all(),
				'working_count' => $this->jobs( 'working' )->count_all(),
				'pending_count' => $this->jobs( 'pending' )->count_all(),
				'failed_count'  => $this->jobs( 'failed' )->count_all(),
			)
		);
	}

	public function action_queued()
	{
		$this->template->set_filename( 'queue/queued' );
		$this->template->set(
			array(
				'jobs' => $this->jobs( $this->request->action() )->find_all()
			)
		);
	}

	public function action_working()
	{
		$this->template->set_filename( 'queue/working' );
		$this->template->set(
			array(
				'jobs' => $this->jobs( $this->request->action() )->find_all()
			)
		);
	}

	public function action_pending()
	{
		$this->template->set_filename( 'queue/pending' );
		$this->template->set(
			array(
				'jobs' => $this->jobs( $this->request->action() )->find_all()
			)
		);
	}

	public function action_failed()
	{
		$this->template->set_filename( 'queue/failed' );
		$this->template->set(
			array(
				'jobs' => $this->jobs( $this->request->action() )->find_all()
			)
		);
	}

	public function action_remove()
	{
		$this->job->delete();
		$this->request->redirect( $_SERVER['HTTP_REFERER'] );
	}

	public function action_clear_failed()
	{
		foreach( $this->jobs( 'failed' )->find_all() as $failed )
		{
			$failed->delete();
		}

		$this->request->redirect( $_SERVER['HTTP_REFERER'] );
	}

	public function action_requeue()
	{
		if( isset( $_REQUEST['all'] ) )
		{
			foreach( $this->jobs( 'failed' )->find_all() as $failed )
			{
				$failed->requeue();			
			}
		}
		else
		{
			$this->job->requeue();
		}

		$this->request->redirect( $_SERVER['HTTP_REFERER'] );
	}

	/**
	 * Define a scope to find jobs by
	 *
	 * @param  String $job
	 * @return ORM
	 */
	protected function jobs( $type )
	{
		$jobs = ORM::factory( 'Job' );

		if( 'working' === $type )
		{
			$jobs = $jobs->where( 'locked_at', 'IS NOT', NULL );
		}
		else if( 'failed' === $type )
		{
			$jobs = $jobs->where( 'failed_at', 'IS NOT', NULL );
		}
		else if( 'pending' === $type )
		{
			$jobs = $jobs->where( 'attempts', '=', 0 );
		}

		return $jobs;
	}
}