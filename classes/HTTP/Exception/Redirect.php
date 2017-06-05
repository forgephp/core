<?php

namespace Forge\HTTP\Exception;

use Forge\URL;
use Forge\Exception;
use Forge\Foundation;
use Forge\HTTP\Exception\Expected;

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
abstract class Redirect extends Expected
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
			throw new Exception( 'A \'location\' must be specified for a redirect' );
		}

		return TRUE;
	}

}
