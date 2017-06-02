<?php

namespace Forge\HTTP;
use Forge\Foundation\Exception as Foundation_Exception;

abstract class Exception extends Foundation_Exception
{
    /**
     * @var  int        http status code
     */
    protected $_code = 0;

    /**
     * @var  Request    Request instance that triggered this exception.
     */
    protected $_request;

    /**
     * Creates an HTTP_Exception of the specified type.
     *
     * @param   integer $code       the http status code
     * @param   string  $message    status message, custom content to display with error
     * @param   array   $variables  translation variables
     * @return  HTTP_Exception
     */
    public static function factory( $code, $message = NULL, array $variables = NULL, Exception $previous = NULL )
    {
        $class = '\Forge\HTTP\Exception\Exception' . $code;

        return new $class( $message, $variables, $previous );
    }

    /**
     * Creates a new translated exception.
     *
     *     throw new Foundation_Exception('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string  $message    status message, custom content to display with error
     * @param   array   $variables  translation variables
     * @return  void
     */
    public function __construct( $message = NULL, array $variables = NULL, Exception $previous = NULL )
    {
        parent::__construct( $message, $variables, $this->_code, $previous );
    }

    /**
     * Store the Request that triggered this exception.
     *
     * @param   Request   $request  Request object that triggered this exception.
     * @return  HTTP_Exception
     */
    public function request( Request $request = NULL )
    {
        if( $request === NULL )
        {
            return $this->_request;
        }

        $this->_request = $request;

        return $this;
    }

    /**
     * Generate a Response for the current Exception
     *
     * @uses   Foundation_Exception::response()
     * @return Response
     */
    public function get_response()
    {
        return Foundation_Exception::response( $this );
    }

}
