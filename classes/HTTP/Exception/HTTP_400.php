<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_400 extends Exception
{
	/**
	 * @var   integer    HTTP 400 Bad Request
	 */
	protected $_code = 400;
}
