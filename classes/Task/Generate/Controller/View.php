<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates template controllers with associated view files. The files can
 * be created in either the application folder or a module folder.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CONTROLLER</info> <alert>(required)</alert>
 *
 *     The name of this controller. If 'Controller_' is not included in
 *     the name, it will be prepended automatically.
 *
 *   <info>--actions=ACTION[,ACTION[,...]]</info>
 *
 *     A comma-separated list of optional action methods to be included in
 *     this controller, without the 'action_' prefix.
 * 
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:controller:view --name=Home --actions="index, create, edit"</info>
 *
 *     class : Controller_Home extends Controller_Template
 *     file  : APPDIR/classes/Controller/Home.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Controller_View extends Task_Generate_Controller
{
	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder( array $options )
	{
		$ds = DIRECTORY_SEPARATOR;

		$view = str_replace( '_', $ds, ( str_replace( 'Controller_', '', $options['name'] ) ) );
		$view = strtolower( $view );

		// Configure the template and add the view file
		return parent::get_builder( $options )
			->template( 'generator/type/controller_view' )
			->set( 'view', $view )
			->add_file( $view . EXT )
			->folder( 'views' )
			->content( 'View for ' . $options['name'] . ' controller' . PHP_EOL )
			->pretend( $options['pretend'] )
			->force( $options['force'] )
			->builder();
	}

}
