<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_502 extends Exception
{
	/**
	 * @var   integer    HTTP 502 Bad Gateway
	 */
	protected $_code = 502;
}
