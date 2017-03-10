<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generator Trait Type
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator_Type_Trait extends Generator_Type
{
	protected $_template = 'generator/type/class';

	protected $_folder   = 'classes';

	protected $_defaults = array(
		'package'    => 'package',
		'category'   => 'Traits',
		'author'     => 'author',
		'copyright'  => 'copyright',
		'license'    => 'license',
		'class_type' => 'trait',
	);

	/**
	 * Adds any traits inherited by this trait.
	 *
	 * The traits may be passed either as an array, comma-separated list
	 * of trait names, or as a single trait name.
	 *
	 * @param   string|array  $traits  The trait names to use
	 * @return  Generator_Type_Trait   This instance
	 */
	public function using( $traits )
	{
		$this->param_to_array( $traits, 'traits' );

		return $this;
	}

}
