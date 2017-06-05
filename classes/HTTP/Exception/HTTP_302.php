<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Redirect;

class HTTP_302 extends Redirect
{
	/**
	 * @var   integer    HTTP 302 Found
	 */
	protected $_code = 302;
}
