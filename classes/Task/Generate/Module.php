<?php

namespace Forge\Task\Generate;

use Forge\Task\Generate;

/**
 * Generates a new module skeleton, with a basic directory structure
 * and initial files.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MODULE</info> <alert>(required)</alert>
 *
 *     The name of the module folder to be created.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:module --name=mymodule</info>
 *
 *     file : MODPATH/mymodule/init.php
 *     file : MODPATH/mymodule/README.md
 *     file : MODPATH/mymodule/LICENSE
 *     file : MODPATH/mymodule/guide/mymodule/menu.md
 *     file : MODPATH/mymodule/guide/mymodule/index.md
 *     file : MODPATH/mymodule/guide/mymodule/start.md
 *     file : MODPATH/mymodule/config/userguide.php
 *     dir  : MODPATH/mymodule/classes
 *     dir  : MODPATH/mymodule/tests
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Module extends Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name' => '',
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
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder( array $options )
	{
		$ds = DIRECTORY_SEPARATOR;

		return Generator::build()

			// Start with an empty init file
			->add_file( 'init.php' )
			->content( Foundation::FILE_SECURITY . PHP_EOL )

			// Readme and license files
			->add_file( 'README.md' )
			->content( '# ' . ucfirst( $options['name'] ) . ' module' . PHP_EOL . PHP_EOL . 'Information about this module.' . PHP_EOL )
			->add_file( 'LICENSE' )
			->content( 'License info' . PHP_EOL )

			// Guide pages and config
			->add_guide( ucfirst( $options['name'] ) )
			->page( 'Getting Started|start' )
			->add_file( 'start.md' )
			->folder( 'guide' . $ds . $options['name'] )
			->content( '# Getting Started' . PHP_EOL . PHP_EOL . 'Coming soon.' . PHP_EOL )
			->add_file( 'index.md' )
			->folder( 'guide' . $ds . $options['name'] )
			->content('# ' . ucfirst( $options['name'] ) . ' module' . PHP_EOL . PHP_EOL . 'Index page for this module.' . PHP_EOL )
			->add_config( 'userguide' )
			->template( 'generator/type/guide_config' )
			->set( 'name', ucfirst( $options['name'] ) )
			->set( 'module', $options['name'] )
			->defaults( $this->get_config( 'defaults.guide', $options['config'] ) )

			// Basic directory structure
			->add_directory( 'classes' )
			->add_directory( 'tests' )

			// Apply global settngs
			->with_module( $options['name'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
			->with_verify( FALSE );
	}

}
