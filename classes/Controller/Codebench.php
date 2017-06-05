<?php

namespace Forge\Controller;

/**
 * Codebench — A benchmarking module.
 *
 * @package    SuperFan
 * @category   Codebench
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Codebench extends Template
{
	// The codebench view
	public $template = 'codebench';

	public function action_index()
	{
		$class = $this->request->param( 'class' );

		// Convert submitted class name to URI segment
		if( isset( $_POST['class'] ) )
		{
			throw HTTP_Exception::factory( 302 )
				->location( '/codebench/' . trim( $_POST['class'] ) )
			;
		}

		// Pass the class name on to the view
		$this->template->class = (string) $class;

		$class = 'Bench_' . $class;

		// Try to load the class, then run it
		if( Foundation::auto_load( $class ) === TRUE )
		{
			$codebench = new $class;

			$this->template->codebench = $codebench->run();
		}
	}

}
