<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_408 extends Exception
{
	/**
	 * @var   integer    HTTP 408 Request Timeout
	 */
	protected $_code = 408;
}
