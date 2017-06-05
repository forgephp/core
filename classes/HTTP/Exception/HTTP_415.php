<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_415 extends Exception
{
	/**
	 * @var   integer    HTTP 415 Unsupported Media Type
	 */
	protected $_code = 415;
}
