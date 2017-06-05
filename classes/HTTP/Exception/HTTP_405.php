<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Expected;

class HTTP_405 extends Expected
{
	/**
	 * @var   integer    HTTP 405 Method Not Allowed
	 */
	protected $_code = 405;

	/**
	 * Specifies the list of allowed HTTP methods
	 *
	 * @param  array $methods List of allowed methods
	 */
	public function allowed( $methods )
	{
		if( is_array( $methods ) )
		{
			$methods = implode( ',', $methods );
		}

		$this->headers( 'allow', $methods );

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
		if( $location = $this->headers( 'allow' ) === NULL )
		{
			throw new Foundation_Exception( 'A list of allowed methods must be specified' );
		}

		return TRUE;
	}
}
