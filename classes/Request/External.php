<?php

namespace Forge\Request;

use Forge\Request\Client;

/**
 * Request_External provides a wrapper for all external request
 * processing. This class should be extended by all drivers handling external
 * requests.
 *
 * Supported out of the box:
 *  - Curl (default)
 *  - PECL HTTP
 *  - Streams
 *
 * To select a specific external driver to use as the default driver, set the
 * following property within the Application bootstrap. Alternatively, the
 * client can be injected into the request object.
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 * @uses       [PECL HTTP](http://php.net/manual/en/book.http.php)
 */
abstract class External extends Client
{
    /**
     * Use:
     *  - Request_Client_Curl (default)
     *  - Request_Client_HTTP
     *  - Request_Client_Stream
     *
     * @var     string    defines the external client to use by default
     */
    public static $client = 'Request_Client_Curl';

    /**
     * Factory method to create a new Request_External object based on
     * the client name passed, or defaulting to Request_External::$client
     * by default.
     *
     * Request_External::$client can be set in the application bootstrap.
     *
     * @param   array   $params parameters to pass to the client
     * @param   string  $client external client to use
     * @return  Request_External
     * @throws  Request_Exception
     */
    public static function factory( array $params = array(), $client = NULL )
    {
        if( $client === NULL )
        {
            $client = Request_External::$client;
        }

        $client = new $client( $params );

        if( ! $client instanceof Request_External )
        {
            throw new Foundation_Exception( 'Selected client is not a Request_External object.' );
        }

        return $client;
    }

    /**
     * @var     array     curl options
     * @link    http://www.php.net/manual/function.curl-setopt
     * @link    http://www.php.net/manual/http.request.options
     */
    protected $_options = array();

    /**
     * Processes the request, executing the controller action that handles this
     * request, determined by the [Route].
     *
     * 1. Before the controller action is called, the [Controller::before] method
     * will be called.
     * 2. Next the controller action will be called.
     * 3. After the controller action is called, the [Controller::after] method
     * will be called.
     *
     * By default, the output from the controller is captured and returned, and
     * no headers are sent.
     *
     *     $request->execute();
     *
     * @param   Request   $request   A request object
     * @param   Response  $response  A response object
     * @return  Response
     * @throws  Foundation_Exception
     */
    public function execute_request( Request $request, Response $response )
    {
        // Store the current active request and replace current with new request
        $previous = Request::$current;
        Request::$current = $request;

        // Resolve the POST fields
        if( $post = $request->post() )
        {
            $request
                ->body( http_build_query( $post, NULL, '&' ) )
                ->headers( 'content-type', 'application/x-www-form-urlencoded; charset=' . Foundation::$charset )
            ;
        }

        $request->headers( 'content-length', (string) $request->content_length() );

        // If Foundation expose, set the user-agent
        if( Foundation::$expose )
        {
            $request->headers( 'user-agent', Foundation::version() );
        }

        try
        {
            $response = $this->_send_message( $request, $response );
        }
        catch( Exception $e )
        {
            // Restore the previous request
            Request::$current = $previous;

            // Re-throw the exception
            throw $e;
        }

        // Restore the previous request
        Request::$current = $previous;

        // Return the response
        return $response;
    }

    /**
     * Set and get options for this request.
     *
     * @param   mixed    $key    Option name, or array of options
     * @param   mixed    $value  Option value
     * @return  mixed
     * @return  Request_External
     */
    public function options( $key = NULL, $value = NULL )
    {
        if( $key === NULL )
        {
            return $this->_options;
        }

        if( is_array( $key ) )
        {
            $this->_options = $key;
        }
        else if( $value === NULL )
        {
            return Arr::get( $this->_options, $key );
        }
        else
        {
            $this->_options[$key] = $value;
        }

        return $this;
    }

    /**
     * Sends the HTTP message [Request] to a remote server and processes
     * the response.
     *
     * @param   Request   $request    Request to send
     * @param   Response  $response   Response to send
     * @return  Response
     */
    abstract protected function _send_message( Request $request, Response $response );

}
