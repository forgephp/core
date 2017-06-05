<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_409 extends Exception
{
	/**
	 * @var   integer    HTTP 409 Conflict
	 */
	protected $_code = 409;
}
