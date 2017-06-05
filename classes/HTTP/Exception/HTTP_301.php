<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Redirect;

class HTTP_301 extends Redirect
{
	/**
	 * @var   integer    HTTP 301 Moved Permanently
	 */
	protected $_code = 301;
}
