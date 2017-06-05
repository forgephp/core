<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

class HTTP_Exception_500 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 500 Internal Server Error
	 */
	protected $_code = 500;

}
