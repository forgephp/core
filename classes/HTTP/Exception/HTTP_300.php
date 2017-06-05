<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Redirect;

class HTTP_300 extends Redirect
{
	/**
	 * @var   integer    HTTP 300 Multiple Choices
	 */
	protected $_code = 300;
}
