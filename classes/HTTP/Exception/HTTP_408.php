<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

class HTTP_Exception_408 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 408 Request Timeout
	 */
	protected $_code = 408;

}
