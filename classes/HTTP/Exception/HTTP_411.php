<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_411 extends Exception
{
	/**
	 * @var   integer    HTTP 411 Length Required
	 */
	protected $_code = 411;
}
