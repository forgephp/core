<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generator File
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator_Type_File extends Generator_Type 
{
	/**
	 * The destination folder for the file
	 * @var string
	 */
	protected $_folder;

	/**
	 * The content of the created file
	 * @var string
	 */
	protected $_content;

	/**
	 * Sets/gets the generated file content.
	 *
	 * @param   string  $content  The file content
	 * @return  string|Generator_Type_File  The file content or this instance
	 */
	public function content( $content )
	{
		if( $content === NULL )
		{
			return $this->_content;
		}

		$this->_content = $content;

		return $this;
	}

	/**
	 * Ensures that the filename is not guessed by converting the name to 
	 * a path, replacing underscores, etc.
	 *
	 * @param   boolean  $convert  Should the name be converted to a file path?
	 * @return  string   The guessed filename
	 * @throws  Generator_Exception  On invalid name or base path
	 */
	public function guess_filename( $convert = FALSE )
	{
		return parent::guess_filename( $convert );
	}

	/**
	 * As we're not using templates, we just need to return the given file
	 * contents directly here.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return $this->_content;
	}
}
