<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_407 extends Exception
{
	/**
	 * @var   integer    HTTP 407 Proxy Authentication Required
	 */
	protected $_code = 407;
}
