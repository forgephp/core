<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

class HTTP_Exception_300 extends HTTP_Exception_Redirect {

	/**
	 * @var   integer    HTTP 300 Multiple Choices
	 */
	protected $_code = 300;

}
