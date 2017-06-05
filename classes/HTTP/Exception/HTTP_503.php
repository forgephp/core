<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_503 extends Exception
{
	/**
	 * @var   integer    HTTP 503 Service Unavailable
	 */
	protected $_code = 503;
}
