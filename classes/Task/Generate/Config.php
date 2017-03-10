<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates configuration files, optionally with simple config entries
 * passed as value definitions or imported from existing sources.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CONFIG</info> <alert>(required)</alert>
 *
 *     This sets the name of the config file.
 *
 *   <info>--values=VALUE[,VALUE[,...]]</info>
 *
 *     Value definitions may be added as a comma-separated list in the
 *     format: "array.path.key|value".
 *
 *   <info>--import=SOURCE[,SOURCE[,...]]</info>
 *
 *     Values may be imported from existing sources as a comma-separated
 *     list in the format: "source|array.path.key", and may be overridden
 *     by any values set via the <info>--values</info> option. If only the source is
 *     specified, all of its values will be imported.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:config --name=logger --module=logger \
 *     --values="logger.file.name|log, logger.file.ext|txt, logger.debug|1"</info>
 *
 *     file : MODPATH/logger/config/logger.php
 *
 * <info>minion generate:config --name=logger --import="app|logger.file, other" \
 *     --values="logger.debug|1, other.name|foo"</info>
 *
 *     file : APPPATH/config/logger.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Config extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'values'  => '',
		'import'  => '',
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'values',
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
			->add_config( $options['name'] )
			->value( $options['values'] )
			->import( $options['import'] )
			->template( $options['template'] )
			->module( $options['module'] )
			->pretend( $options['pretend'] )
			->force( $options['force'] )
			->builder()
		;
	}

}
