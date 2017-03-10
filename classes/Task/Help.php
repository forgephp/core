<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Help task to display general instructons and list all tasks
 *
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Task_Help extends Minion_Task
{
	/**
	 * Generates a help list for all tasks
	 *
	 * @return null
	 */
	protected function _execute( array $params )
	{
		$tasks = $this->_compile_task_list( Foundation::list_files( 'classes/Task' ) );

		$view = new View( 'minion/list' );

		$view->tasks = $tasks;

		echo $view;
	}
}
