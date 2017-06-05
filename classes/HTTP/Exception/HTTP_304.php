<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception\Expected;

class HTTP_304 extends Expected
{
	/**
	 * @var   integer    HTTP 304 Not Modified
	 */
	protected $_code = 304;
}
