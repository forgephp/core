<?php

namespace Forge\Task\Generate;

use Forge\Task\Generate;

/**
 * Generates message files, optionally with simple message entries
 * passed as value definitions or imported from existing sources.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MESSAGE</info> <alert>(required)</alert>
 *
 *     This sets the name of the message file.
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
 * <info>minion generate:message --name=logger --module=logger \
 *     --values="logging.some_message|some_value"</info>
 *
 *     file : MODPATH/logger/messages/logger.php
 *
 * <info>minion generate:message --name=logger --import="app|logging, other" \
 *     --values="logging.some_message|some_value"</info>
 *
 *     file : APPPATH/messages/logger.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Message extends Generate
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
	 * @param   array  $options  the selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder( array $options )
	{
		return Generator::build()
			->add_message( $options['name'] )
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
