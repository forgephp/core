<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_404 extends Exception
{
	/**
	 * @var   integer    HTTP 404 Not Found
	 */
	protected $_code = 404;
}
