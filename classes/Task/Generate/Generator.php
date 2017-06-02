<?php

namespace Forge\Task\Generate;

use Forge\Task\Generate;

/**
 * Generates new Generator types from templates, together with associated unit
 * tests and Minion tasks. If a module is specified, both the generators and
 * tasks will be extended transparently with stubs.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TYPE</info> <alert>(required)</alert>
 *
 *     The name of the generator type. If `Generator_Type_` is not included
 *     in the name, it will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is optionally extended,
 *     otherwise defaults to Generator_Type.
 *
 *   <info>--prefix=PREFIX</info>
 *
 *     If created in a module and extended transparently with stubs, the
 *     classes will be prefixed with the module name by default unless the
 *     value is set with this option.
 *
 *   <info>--no-stub</info>
 *
 *     The classes will not be extended transparently if this option is set.
 * 
 *   <info>--no-task</info>
 *
 *     Minion tasks will not be created if this option is set.
 *
 *   <info>--no-test</info>
 *
 *     Unit tests will not be created if this option is set. 
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:generator --name=Foo</info>
 *
 *     class : Generator_Type_Foo extends Generator_Type
 *     file  : APPPATH/classes/Generator/Type/Foo.php
 *     class : Task_Generate_Foo extends Task_Generate
 *     file  : APPPATH/classes/Task/Generate/Foo.php
 *     class : Generator_Type_FooTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Generator/Type/FooTest.php
 *
 * <info>minion generate:generator --name=Foo --module=bar --no-task --no-test</info>
 *
 *     class : Bar_Generator_Type_Foo extends Generator_Type
 *     file  : MODPATH/bar/classes/Bar/Generator/Type/Foo.php
 *     class : Generator_Type_Foo extends Bar_Generator_Type_Foo
 *     file  : MODPATH/bar/classes/Generator/Type/Foo.php 
 *
 * <info>minion generate:generator --name=Foo --module=bar --no-test --prefix=Kohana</info>
 *
 *     class : Kohana_Generator_Type_Foo extends Generator_Type
 *     file  : MODPATH/bar/classes/Kohana/Generator/Type/Foo.php
 *     class : Generator_Type_Foo extends Kohana_Generator_Type_Foo
 *     file  : MODPATH/bar/classes/Generator/Type/Foo.php
 *     class : Kohana_Task_Generate_Foo extends Task_Generate
 *     file  : MODPATH/bar/classes/Kohana/Task/Generate/Foo.php
 *     class : Task_Generate_Foo extends Kohana_Task_Generate
 *     file  : MODPATH/bar/classes/Task/Generate/Foo.php
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
		'no-task' => FALSE,
		'no-test' => FALSE,
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
		// Set any class prefix, default is the module name
		$prefix = $options['prefix'] ?: ucfirst( $options['module'] );

		$builder = Generator::build()
			->add_generator( $options['name'] )
			->extend( $options['extend'] )
			->template( $options['template'] )
			->builder()
		;

		if( $options['module'] && ! $options['no-stub'] )
		{
			// Prefix the generator name
			$name = $prefix . '_' . $builder->name();

			$builder->name( $name );

			// Add a stub to extend the generator transparently
			$builder->add_generator( $options['name'] )
				->extend( $name )
				->template( 'generator/type/stub' )
				->set( 'source', 'Generator ' . $options['name'] . ' type' )
			;
		}

		if( ! $options['no-task'] )
		{
			$builder->add_task( 'Generate_' . $options['name'] )
				->template( 'generator/type/task_generator' )
				->extend( 'Task_Generate' )
			;

			if( $options['module'] && ! $options['no-stub'] )
			{
				// Prefix the task name
				$name = $prefix . '_' . $builder->name();

				$builder
					->name( $name )
					->set( 'category', 'Generator/Tasks' )
					->no_help()
				;

				// Add a stub to extend the task transparently
				$builder->add_task( 'Generate_' . $options['name'] )
					->extend( $name )
					->blank()
				;
			}
		}

		if( ! $options['no-test'] )
		{
			$builder
				->add_unittest( 'Generator_Type_' . $options['name'] )
				->group( 'generator' )
				->group( 'generator.types' )
			;
		}

		return $builder
			->with_module( $options['module'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) )
		;
	}

}
