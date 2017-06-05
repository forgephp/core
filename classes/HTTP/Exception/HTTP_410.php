<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_410 extends Exception
{
	/**
	 * @var   integer    HTTP 410 Gone
	 */
	protected $_code = 410;
}
