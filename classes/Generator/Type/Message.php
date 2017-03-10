<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generator Message Type
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Generator_Type_Message extends Generator_Type_Config
{
	protected $_folder = 'messages';

	protected function _import_source( $source, $path = NULL )
	{
		return Generator::get_message( $source, $path );
	}

}
