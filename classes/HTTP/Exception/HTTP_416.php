<?php

namespace Forge\HTTP\Exception;

use Forge\HTTP\Exception;

class HTTP_416 extends Exception
{
	/**
	 * @var   integer    HTTP 416 Request Range Not Satisfiable
	 */
	protected $_code = 416;
}
