<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_417 extends Exception
{
	/**
	 * @var   integer    HTTP 417 Expectation Failed
	 */
	protected $_code = 417;
}
