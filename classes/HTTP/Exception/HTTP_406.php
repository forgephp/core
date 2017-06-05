<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_406 extends Exception
{
	/**
	 * @var   integer    HTTP 406 Not Acceptable
	 */
	protected $_code = 406;
}
