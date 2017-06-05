<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_412 extends Exception
{
	/**
	 * @var   integer    HTTP 412 Precondition Failed
	 */
	protected $_code = 412;
}
