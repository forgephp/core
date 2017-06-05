<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_504 extends Exception
{
	/**
	 * @var   integer    HTTP 504 Gateway Timeout
	 */
	protected $_code = 504;
}
