<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_505 extends Exception
{
	/**
	 * @var   integer    HTTP 505 HTTP Version Not Supported
	 */
	protected $_code = 505;
}
