<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates Guide menu, index and optionally page files with skeleton
 * content, and the userguide config file. The menu will include entries
 * for any given page definitions.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MENU</info> <alert>(required)</alert>
 *
 *     This will set the top level of the Guide menu in the menu file.
 *
 *   <info>--pages=PAGES</info>
 *
 *     Page definitions may be added as a comma-separated list in the
 *     format: "Page Title|filename".
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:guide --name=Logging --module=logger \
 *     --pages="Setting up|setup, Running the tasks|tasks"</info>
 *
 *     file : MODPATH/logger/guide/logger/menu.md
 *     file : MODPATH/logger/guide/logger/index.md
 *     file : MODPATH/logger/guide/logger/setup.md
 *     file : MODPATH/logger/guide/logger/tasks.md
 *     file : MODPATH/logger/config/userguide.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Guide extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'  => '',
		'pages' => '',
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'pages',
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
		// Choose the folder in which to create the guide files
		$folder = $options['module'] ? ( 'guide' . DIRECTORY_SEPARATOR . $options['module'] ) : 'guide';

		// Start by creating the guide menu
		$builder = Generator::build()
			->add_guide( $options['name'] )
			->folder( $folder )
			->page( $options['pages'] )
			->builder()
		;

		if( $options['pages'] )
		{
			// Get any guide page definitions
			$params = $builder->params();
			$pages = $builder->get_menu_pages();

			foreach( $pages as $title => $file )
			{
				// Add any defined page files
				$builder
					->add_file( $file . '.md' )
					->folder( $folder )
					->content( '# ' . $title . PHP_EOL . PHP_EOL . 'Content of this page.' . PHP_EOL )
				;
			}
		}

		// Add the index file
		$builder
			->add_file( 'index.md' )
			->folder( $folder )
			->content( '# ' . $options['name'] . PHP_EOL . PHP_EOL . 'Content of the index page.' . PHP_EOL )
		;

		if( $options['module'] )
		{
			// Add a config file
			$builder
				->add_config( 'userguide' )
				->template( 'generator/type/guide_config' )
				->set( 'name', ucfirst( $options['name'] ) )
				->set( 'module', $options['module'] )
				->defaults( $this->get_config( 'defaults.guide', $options['config'] ) )
			;
		}

		// Return the builder
		return $builder
			->with_module( $options['module'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
		;
	}

}
