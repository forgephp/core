<?php

namespace Forge\Task;

use Forge\View;
use Forge\Foundation;
use Forge\Minion\Task;

/**
 * Help task to display general instructons and list all tasks
 *
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Help extends Task
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
