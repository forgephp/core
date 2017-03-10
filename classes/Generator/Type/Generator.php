<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generator Type for creating generators.
 *
 * Yes, this is SUPER meta.
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator_Type_Generator extends Generator_Type_Class
{
	protected $_template = 'generator/type/generator';

	protected $_folder   = 'classes';

	/**
	 * Sets/gets the type class name.
	 *
	 * @param   string  $name  The type name
	 * @return  string|Generator_Type_Generator  The current name or this instance
	 */
	public function name( $name = NULL )
	{
		if( $name == NULL )
		{
			return $this->_name;
		}

		// Prepend 'Generator_Type_' to the class name if not already present
		if( strpos( $name, 'Generator_Type_' ) === FALSE )
		{
			$name = 'Generator_Type_' . $name;
		}

		$this->_name = $name;

		return $this;
	}

	/**
	 * Gets the short type name from the class name.
	 *
	 * @return  string  The short type name
	 */
	public function get_type_name()
	{
		$type = explode( '_', $this->_name );

		return array_pop( $type );
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if( empty( $this->_params['category'] ) )
		{
			$this->_params['category'] = 'Generator/Types';
		}

		if( empty( $this->_params['extends'] ) )
		{
			$this->_params['extends'] = 'Generator_Type';
		}

		$type = $this->get_type_name();

		$this->_params['type'] = $type;

		if( empty( $this->_params['type_template'] ) )
		{
			$this->_params['type_template'] = 'type/' . strtolower( $type );
		}

		return parent::render();
	}
}
