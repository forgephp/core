<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

class HTTP_Exception_504 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 504 Gateway Timeout
	 */
	protected $_code = 504;

}
