<?php

namespace Forge\Minion\Exception;

use Forge\Minion\Exception;

/**
 * Invalid Task Exception
 *
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class InvalidTask extends Exception
{
	public function format_for_cli()
	{
		return 'ERROR: ' . $this->getMessage() . PHP_EOL;
	}

}
