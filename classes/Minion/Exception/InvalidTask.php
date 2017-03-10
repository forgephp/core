<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Invalid Task Exception
 *
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Minion_Exception_InvalidTask extends Minion_Exception
{
	public function format_for_cli()
	{
		return 'ERROR: ' . $this->getMessage() . PHP_EOL;
	}

}
