<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Redirect HTTP exception class. Used for all HTTP_Exception's where the status
 * code indicates a redirect.
 *
 * Eg HTTP_Exception_301, HTTP_Exception_302 and most of the other 30x's
 *
 * @package    SuperFan
 * @category   Exceptions
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
abstract class HTTP_Exception_Redirect extends HTTP_Exception_Expected
{
	/**
	 * Specifies the URI to redirect to.
	 *
	 * @param  string  $location  URI of the proxy
	 */
	public function location( $uri = NULL )
	{
		if( $uri === NULL )
		{
			return $this->headers( 'Location' );
		}

		if( strpos( $uri, '://' ) === FALSE )
		{
			// Make the URI into a URL
			$uri = URL::site( $uri, TRUE, ! empty( Foundation::$index_file ) );
		}

		$this->headers( 'Location', $uri );

		return $this;
	}

	/**
	 * Validate this exception contains everything needed to continue.
	 *
	 * @throws Foundation_Exception
	 * @return bool
	 */
	public function check()
	{
		if( $this->headers( 'location' ) === NULL )
		{
			throw new Foundation_Exception( 'A \'location\' must be specified for a redirect' );
		}

		return TRUE;
	}

}
