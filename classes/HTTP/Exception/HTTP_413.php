<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_413 extends Exception
{
	/**
	 * @var   integer    HTTP 413 Request Entity Too Large
	 */
	protected $_code = 413;
}
