<?php

namespace Forge\Task\Generate\Task;

use Forge\Task\Generate;

/**
 * Creates custom generator tasks that can be evoked with generate:TASK.
 * The task can be created in either the application folder or a module
 * folder, and can optionally be configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TASK</info> <alert>(required)</alert>
 *
 *     The name of this task. The class pefixes will be added automatically,
 *     so e.g. don't include 'Task_Generate' in the name.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Task_Generate will be used by default.
 *
 *   <info>--prefix=PREFIX</info>
 *
 *     If created in a module and extended transparently with a stub, the
 *     task will be prefixed with the module name by default unless the
 *     value is set with this option.
 *
 *   <info>--no-stub</info>
 *
 *     The task will not be extended transparently if this option is set. 
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:task:generator --name=Log</info>
 *
 *     class : Task_Generate_Log extends Task_Generate
 *     file  : APPDIR/classes/Task/Generate/Log.php
 *
 * <info>minion generate:task:generator --name=Log --module=foundation</info>
 *
 *     class : Task_Generate_Log extends Task_Generate
 *     file  : FOUNDATION/classes/Task/Generate/Log.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator extends Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'extend'  => '',
		'prefix'  => '',
		'no-stub' => FALSE,
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
		// Set any class prefix, default is the module name
		$prefix = $options['prefix'] ?: ucfirst( $options['module'] );

		// Set the default template and extension
		$template = $options['template'] ?: 'generator/type/task_generator';
		$extend = $options['extend'] ?: 'Task_Generate';

		$builder = Generator::build()
			->add_task( 'Generate_' . $options['name'] )
			->extend( $extend )
		;

		if( $options['module'] && ! $options['no-stub'] )
		{
			// Prefix the task name
			$name = $prefix.'_'.$builder->name();

			$builder->name( $name )
				->set( 'category', 'Generator/Tasks' )
				->no_help()
			;

			// Add a stub to extend the task transparently
			$builder->add_task( 'Generate_' . $options['name'] )
				->extend( $name )
				->blank()
			;
		}

		return $builder
			->with_template( $template )
			->with_module( $options['module'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) )
		;
	}

}
