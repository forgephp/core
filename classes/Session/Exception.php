<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * @package    SuperFan
 * @category   Session
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Session_Exception extends Foundation_Exception
{
	const SESSION_CORRUPT = 1;
}
