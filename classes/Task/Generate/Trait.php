<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application traits from templates. The trait can be created in
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension <alert>(requires PHP >= 5.4.0)</alert>.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TRAIT</info> <alert>(required)</alert>
 *
 *     The full name of the trait to be created, with capitalization.
 *
 *   <info>--stub=TRAIT</info>
 *
 *     If set, this empty trait will be created as a transparent extension
 *     of the main trait.
 *
 *   <info>--use=TRAIT[,TRAIT[,...]]</info>
 *
 *     A comma-separated list of any traits that this trait should use.
 *
 *   <info>--clone=TRAIT</info>
 *
 *     If a valid trait name is set with this option, its methods will be
 *     copied directly from its class file. Reflection  will be used if the
 *     <info>--reflect</info> option is set, and inherited methods may be included with
 *     the <info>--inherit</info> option.
 *
 *   <info>--blank</info>
 *
 *     The trait's skelton methods will be omitted if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:trait --name=Trait_Logging</info>
 *
 *     trait : Trait_Logging
 *     file  : APPPATH/classes/Trait/Logging.php
 *
 * <info>minion generate:trait --name=Logger_Trait_Logging --module=logger \
 *     --use="Sorter, Counter" --stub=Trait_Logging</info>
 *
 *     trait : Logger_Trait_Logging {use Sorter; use Counter;}
 *     file  : MODPATH/logger/classes/Logger/Trait/Logging.php
 *     trait : Trait_Logging {use Logger_Trait_Logging;}
 *     file  : MODPATH/logger/classes/Trait/Logging.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Trait extends Task_Generate_Class
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'use'       => '',
		'stub'      => '',
		'blank'     => FALSE,
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
			$builder = $this->get_clone( $options, Generator_Reflector::TYPE_TRAIT );
			$builder->set( 'category', 'Traits' );
		}
		else
		{
			$builder = Generator::build()
				->add_trait( $options['name'] )
				->using( $options['use'] )
				->template( $options['template'] )
				->blank( $options['blank'] )
				->builder()
			;
		}

		if( $options['stub'] )
		{
			$builder
				->add_trait( $options['stub'] )
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
