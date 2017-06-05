<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_500 extends Exception
{
	/**
	 * @var   integer    HTTP 500 Internal Server Error
	 */
	protected $_code = 500;
}
