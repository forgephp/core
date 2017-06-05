<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Redirect;

class HTTP_307 extends Redirect
{
	/**
	 * @var   integer    HTTP 307 Temporary Redirect
	 */
	protected $_code = 307;
}
