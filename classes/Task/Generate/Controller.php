<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application controllers from templates. The controller can be
 * created in either the application folder or a module folder.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CONTROLLER</info> <alert>(required)</alert>
 *
 *     The name of this controller. If 'Controller_' is not included in
 *     the name, it will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Controller will be used by default.
 *
 *   <info>--actions=ACTION[,ACTION[,...]]</info>
 *
 *     A comma-separated list of optional action methods to be included in
 *     this controller, without the 'action_' prefix.
 * 
 *   <info>--blank</info>
 *
 *     The skelton methods will be omitted if this option is set.
 * 
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:controller --name=Home</info>
 *
 *     class : Controller_Home extends Controller
 *     file  : APPPATH/classes/Controller/Home.php
 *
 * <info>minion generate:controller --name=Home --module=logger --blank \
 *     --extend=Controller_Template --actions="index, create, edit"</info>
 *
 *     class : Controller_Home extends Controller_Template
 *     file  : MODPATH/logger/classes/Controller/Home.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Controller extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'actions' => '',
		'extend'  => '',
		'blank'   => FALSE,
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'actions',
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
		return Generator::build()
			->add_controller( $options['name'] )
			->extend( $options['extend'] )
			->action( $options['actions'] )
			->blank( $options['blank'] )
			->module( $options['module'] )
			->template( $options['template'] )
			->pretend( $options['pretend'] )
			->force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) )
		;
	}

}
