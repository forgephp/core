<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Redirect;

class HTTP_303 extends Redirect
{
	/**
	 * @var   integer    HTTP 303 See Other
	 */
	protected $_code = 303;
}
