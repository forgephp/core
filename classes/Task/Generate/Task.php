<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application tasks from templates. The task can be created in 
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TASK</info> <alert>(required)</alert>
 *
 *     The name of this task. If 'Task_' is not included in the name, it
 *     will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Minion_Task will be used by default.
 *
 *   <info>--stub=TASK</info>
 *
 *     If set, this empty task will be created as a transparent extension,
 *     and usage info will be added to the stub instead; the 'Task_' prefix
 *     may also be omitted from the stub name.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:task --name=Logs_Rotate</info>
 *
 *     class : Task_Logs_Rotate extends Minion_Task
 *     file  : APPPATH/classes/Task/Logs/Rotate.php
 *
 * <info>minion generate:task --name=Logger_Task_Logs_Rotate --module=logger \
 *     --stub=Logs_Rotate</info>
 *
 *     class : Logger_Task_Logs_Rotate extends Minion_Task
 *     file  : MODPATH/logger/classes/Logger/Task/Logs/Rotate.php
 *     class : Task_Logs_Rotate extends Logger_Task_Logs_Rotate
 *     file  : MODPATH/logger/classes/Task/Logs/Rotate.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Task extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'     => '',
		'extend'   => '',
		'stub'     => '',
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
		$builder = Generator::build()
			->add_task( $options['name'] )
			->extend( $options['extend'] )
			->builder()
		;

		if( $options['stub'] )
		{
			$builder->no_help();
			$parent = $builder->name();

			$builder
				->add_task( $options['stub'] )
				->extend( $parent )
				->blank()
			;
		}

		return $builder
			->with_module( $options['module'] )
			->with_template( $options['template'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) )
		;
	}

}
