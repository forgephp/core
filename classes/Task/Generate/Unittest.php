<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates a unit test case with optional skeleton methods.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TEST</info> <alert>(required)</alert>
 *
 *     The full class name of this test. The 'Test' suffix will be added
 *     automatically if not already included in the name.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which the test case is extended,
 *     if none is given then Unittest_TestCase will be used by default.
 *
 *   <info>--groups=GROUP[,GROUP[,...]]</info>
 *
 *     A comma-separated list of the group parameters for this test case.
 *
 *   <info>--blank</info>
 *
 *     The skelton methods will be omitted if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:test --name=Logger_Logs_Rotate --groups="logger,logger.tasks"</info>
 *
 *     class : Logger_Logs_RotateTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Logger/Logs/RotateTest.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Unittest extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'   => '',
		'groups' => '',
		'extend' => '',
		'blank'  => FALSE,
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'groups',
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
			->add_unittest( $options['name'] )
			->group( $options['groups'] )
			->extend( $options['extend'] )
			->blank( $options['blank'] )
			->module( $options['module'] )
			->template( $options['template'] )
			->pretend( $options['pretend'] )
			->force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) )
		;
	}

}
