<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application classes from templates. The class can be created in
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CLASS</info> <alert>(required)</alert>
 *
 *     The full name of the class to be created, with capitalization.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is optionally extended.
 *
 *   <info>--stub=CLASS</info>
 *
 *     If set, this empty class will be created as a transparent extension
 *     of the main class.
 *
 *   <info>--implement=INTERFACE[,INTERFACE[,...]]</info>
 *
 *     A comma-separated list of any interfaces that this class should
 *     implement.
 *
 *   <info>--use=TRAIT[,TRAIT[,...]]</info>
 *
 *     A comma-separated list of any traits that this class should use
 *     <alert>(requires PHP >= 5.4.0)</alert>.
 *
 *   <info>--clone=CLASS</info>
 *
 *     If a valid class name is set with this option, its properties and
 *     methods will be copied directly from its class file.  Reflection
 *     will be used for internal classes, or if the <info>--reflect</info> option is set,
 *     and inherited methods and properties may be included with <info>--inherit</info>.
 *
 *   <info>--abstract</info>
 *
 *     The class will be marked as abstract if this option is set.
 *   <info>--no-test</info>
 *
 *     A test case will be created automatically for the class unless this
 *     option is set.
 *
 *   <info>--blank</info>
 *
 *     The skelton methods for both the class and the test will be omitted
 *     if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:class --name=Log_Reader --implement=Countable,ArrayAccess</info>
 *
 *     class : Log_Reader implements Countable, ArrayAccess
 *     file  : APPPATH/classes/Log/Reader.php
 *     class : Log_ReaderTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Log/ReaderTest.php
 *
 * <info>minion generate:class --name=Logger_Log_Reader --extend=Logger_Reader \
 *     --module=logger --stub=Log_Reader --no-test</info>
 *
 *     class : Logger_Log_Reader extends Logger_Reader
 *     file  : MODPATH/logger/classes/Logger/Log/Reader.php
 *     class : Log_Reader extends Logger_Log_Reader
 *     file  : MODPATH/logger/classes/Log/Reader.php
 *
 * <info>minion generate:class --name=Log --clone=SplMinHeap --inherit --no-test</info>
 *
 *     class : Log extends SplHeap implements Countable, Traversable, Iterator
 *     file  : APPPATH/classes/Log.php
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate_Class extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'extend'    => '',
		'implement' => '',
		'use'       => '',
		'stub'      => '',
		'abstract'  => FALSE,
		'no-test'   => FALSE,
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
			$builder = $this->get_clone( $options );
		}
		else
		{
			$builder = Generator::build()
				->add_class( $options['name'] )
				->as_abstract( ( $options['abstract'] ) )
				->extend( $options['extend'] )
				->implement( $options['implement'] )
				->using( $options['use'] )
				->template( $options['template'] )
				->blank( $options['blank'] )
				->builder()
			;
		}

		if( $options['stub'] )
		{
			$builder
				->add_class( $options['stub'] )
				->as_abstract( ( $options['abstract'] ) )
				->extend( $options['name'] )
				->template( 'generator/type/stub' )
				->set( 'source', $options['name'] )
			;
		}

		if( ! $options['no-test'] )
		{
			$name = $options['stub'] ? $builder->name() : $options['name'];

			$builder
				->add_unittest( $name )
				->group( $options['module'] )
				->blank( $options['blank'] )
			;
		}

		return $builder
			->with_module( $options['module'] )
			->with_pretend( $options['pretend'] )
			->with_force( $options['force'] )
			->with_defaults( $this->get_config( 'defaults.class', $options['config'] ) );
	}

	/**
	 * Creates a generator builder that clones an existing class, either from
	 * an existing file or from an internal class definition.
	 *
	 * @param   array  $options  The selected task options
	 * @param   array  $type     The source type to clone
	 * @return  Generator_Builder
	 */
	public function get_clone( array $options, $type = Generator_Reflector::TYPE_CLASS )
	{
		// Convert the cloned class name to a filename
		$source = str_replace( '_', DIRECTORY_SEPARATOR, $options['clone'] );

		if( ! $options['reflect'] && ( $file = Foundation::find_file( 'classes', $source ) ) )
		{
			// Use the existing class file
			$content = file_get_contents( $file );

			// Replace the class name references
			$content = preg_replace( "/\b{$options['clone']}\b/", $options['name'], $content );

			// Convert the generated class name to a filename
			$destination = str_replace( '_', DIRECTORY_SEPARATOR, $options['name'] ) . EXT;

			// Create the Builder
			$builder = Generator::build()
				->add_file( $destination )
				->folder( 'classes' )
				->content( $content )
				->builder()
			;
		}
		else
		{
			// Use the internal class definition via reflection
			$builder = Generator::build()
				->add_clone( $options['name'] )
				->source( $options['clone'] )
				->type( $type )
				->inherit( $options['inherit'] )
				->builder()
			;
		}

		return $builder;
	}

}
