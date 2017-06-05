<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_414 extends Exception
{
	/**
	 * @var   integer    HTTP 414 Request-URI Too Long
	 */
	protected $_code = 414;
}
