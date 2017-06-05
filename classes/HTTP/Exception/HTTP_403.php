<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_403 extends Exception
{
	/**
	 * @var   integer    HTTP 403 Forbidden
	 */
	protected $_code = 403;
}
