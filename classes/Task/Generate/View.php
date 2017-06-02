<?php

namespace Forge\Task\Generate;

use Forge\Task\Generate;

/**
 * Generates a simple view template file.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=VIEW</info> <alert>(required)</alert>
 *
 *     The name of the view template to be created in the views folder,
 *     without the file extension.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:view --name=foo/bar</info>
 *
 *     file : APPPATH/views/foo/bar.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class View extends Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'   => '',
	);

	/**
	 * Validates the task options.
	 *
	 * @param   Validation  $validation  The validation object to add rules to
	 * @return  Validation
	 */
	public function build_validation( Validation $validation )
	{
		return parent::build_validation( $validation )
			->rule( 'name', 'not_empty' )
		;
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  the selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder( array $options )
	{
		return Generator::build()
			->add_file( $options['name'] . EXT )
			->folder( 'views' )
			->module( $options['module'] )
			->pretend( $options['pretend'] )
			->force( $options['force'] )
			->content( 'Content of view ' . $options['name'] . PHP_EOL )
			->builder()
		;
	}

}
