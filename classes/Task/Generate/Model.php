<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application models from templates. The model can be created in 
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MODEL</info> <alert>(required)</alert>
 *
 *     The name of this Model. If 'Model' is not included in the name, it
 *     will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is optionally extended.
 *
 *   <info>--stub=MODEL</info>
 *
 *     If set, this stub will be created as a transparent extension of the 
 *     model; the 'Model' prefix may also be omitted.
 *
 *   <info>--no-test</info>
 *
 *     A test case will be created automatically for the class unless this
 *     option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:model --name=Log</info>
 *
 *     class : Model_Log extends Model
 *     file  : APPPATH/classes/Model/Log.php
 *     class : Model_LogTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Model/LogTest.php 
 *
 * <info>minion generate:model --name=Logger_Model_Log --extend=Model_Database \
 *     --module=logger --stub=Model_Log --no-test</info>
 *
 *     class : Logger_Model_Log extends Model_Database
 *     file  : MODPATH/logger/classes/Logger/Model/Log.php
 *     class : Model_Log extends Logger_Model_Log
 *     file  : MODPATH/logger/classes/Logger/Model/Log.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Model extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'extend'    => '',
		'stub'      => '',
		'no-test'   => FALSE,
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
		$builder = Generator::build()
			->add_model( $options['name'] )
			->extend( $options['extend'] )
			->template( $options['template'] )
			->builder()
		;

		$model = $builder->name();

		if( $options['stub'] )
		{
			$builder
				->add_model( $options['stub'] )
				->extend( $model )
				->template( 'generator/type/stub' )
				->set( 'source', $model )
			;
		}

		if( ! $options['no-test'] )
		{
			$name = $options['stub'] ? $builder->name() : $model;

			$builder
				->add_unittest( $name )
				->group( $options['module'] )
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
