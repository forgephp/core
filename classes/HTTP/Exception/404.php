<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

class HTTP_Exception_404 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 404 Not Found
	 */
	protected $_code = 404;

}
