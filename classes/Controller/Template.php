<?php

namespace Forge\Controller;

use Forge\Controller;

/**
 * Abstract controller class for automatic templating.
 *
 * @package    SuperFan
 * @category   Migrations
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
abstract class Template extends Controller
{
	/**
	 * @var  View  page template
	 */
	public $template = 'template';

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		parent::before();

		if( $this->auto_render === TRUE )
		{
			// Load the template
			$this->template = View::factory( $this->template );
		}
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if( $this->auto_render === TRUE )
		{
			$this->response->body( $this->template->render() );
		}

		parent::after();
	}
}
