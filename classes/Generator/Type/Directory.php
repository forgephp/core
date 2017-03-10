<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generator Directory
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator_Type_Directory extends Generator_Type
{
	protected $_force = FALSE;

	/**
	 * Ensures that the directory is not converted to a file name.
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
	 * Existing directories should not be replaced, so don't allow the force
	 * mode to be changed.
	 *
	 * @param   boolean  $force  The force mode to be used
	 * @return  Generator_Type   This instance
	 */
	public function force( $force = TRUE )
	{
		return $this;
	}

	/**
	 * This is a directory, so just return a message for inspect() output.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return 'This is a directory type, nothing to render.' . PHP_EOL;
	}

	/**
	 * Deletes the directory and its parents if not empty.
	 *
	 * @return  Generator_Type  This instance
	 * @throws  Generator_Exception  On invalid directory name
	 */
	public function remove()
	{
		if( ! $this->_file && ! $this->guess_filename() )
		{
			// We can't continue without a valid path
			throw new Generator_Exception( 'Directory name could not be determined' );
		}

		// Start a fresh log
		$this->_log = array();

		$child = $this->_file;

		// Check the main directory
		if( $this->item_exists( $child, FALSE ) )
		{
			if( ! $this->dir_is_empty( $child ) )
			{
				// The directory isn't empty, so leave it be
				$this->log( 'not empty', $child );

				return $this;
			}

			// Remove the directory
			$this->log( 'remove', $child );

			$this->_pretend || rmdir( $child );
		}

		// Check the parent directories
		foreach( $this->get_item_dirs( TRUE ) as $parent )
		{
			if( $this->item_exists( $parent, FALSE ) )
			{
				if( ! $this->dir_is_empty( $parent, $child ) )
				{
					// Stop on non-empty directories
					$this->log( 'not empty', $parent );

					break;
				}

				// Remove the directory
				$this->log( 'remove', $parent );
				$this->_pretend || rmdir( $parent );
			}
		}

		return $this;
	}
}
