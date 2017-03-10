<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application interfaces from templates. The interface can be
 * created in either the application folder or a module folder, and can
 * optionally be configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=INTERFACE</info> <alert>(required)</alert>
 *
 *     The full name of the interface to be created, with capitalization.
 *
 *   <info>--extend=INTERFACE[,INTERFACE[,...]]</info>
 *
 *     A comma-separated list of any interfaces that this interface should 
 *     extend (multiple inheritance is possible).
 *
 *   <info>--clone=INTERFACE</info>
 *
 *     If a valid interface name is given, its definition will be copied
 *     directly from its file.  Reflection will be used for any internal
 *     interfaces, or if the <info>--reflect</info> option is set, and any inherited
 *     method definitions may be included with <info>--inherit</info>.
 *
 *   <info>--stub=INTERFACE</info>
 *
 *     If set, this stub will be created as a transparent extension.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:interface --name=Loggable --extend=Countable,Iterator</info>
 *
 *     interface : Loggable extends Countable, Iterator
 *     file      : APPPATH/classes/Loggable.php
 *
 * <info>minion generate:interface --name=Logger_Loggable --stub=Loggable \
 *     --module=logger</info>
 *
 *     interface : Logger_Loggable
 *     file      : MODPATH/logger/classes/Logger/Loggable.php
 *     interface : Loggable extends Logger_Loggable
 *     file      : MODPATH/logger/classes/Loggable.php 
 *
 * <info>minion generate:interface --name=Loggable --clone=SeekableIterator</info>
 *
 *     interface : Loggable extends Traversable
 *     file      : APPPATH/classes/Log.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Interface extends Task_Generate_Class
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'extend'    => '',
		'stub'      => '',
		'clone'     => '',
		'reflect'   => FALSE,
		'inherit'   => FALSE,
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
		if( ! empty( $options['clone'] ) )
		{
			// Get the clone via Task_Generate_Class::get_clone
			$builder = $this->get_clone( $options, Generator_Reflector::TYPE_INTERFACE );
			$builder->set( 'category', 'Interfaces' );
		}
		else
		{
			$builder = Generator::build()
				->add_interface( $options['name'] )
				->template( $options['template'] )
				->extend( $options['extend'] )
				->builder()
			;
		}

		if( $options['stub'] )
		{
			$builder
				->add_interface( $options['stub'] )
				->extend( $options['name'] )
				->template( 'generator/type/stub' )
				->set( 'source', $options['name'] )
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
