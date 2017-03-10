<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Cookie helper.
 *
 * @package    SuperFan
 * @category   Cookie
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Cookie
{
    /**
     * @var  string  Magic salt to add to the cookie
     */
    public static $salt = NULL;

    /**
     * @var  integer  Number of seconds before the cookie expires
     */
    public static $expiration = 0;

    /**
     * @var  string  Restrict the path that the cookie is available to
     */
    public static $path = '/';

    /**
     * @var  string  Restrict the domain that the cookie is available to
     */
    public static $domain = NULL;

    /**
     * @var  boolean  Only transmit cookies over secure connections
     */
    public static $secure = FALSE;

    /**
     * @var  boolean  Only transmit cookies over HTTP, disabling Javascript access
     */
    public static $httponly = FALSE;

    /**
     * Gets the value of a signed cookie. Cookies without signatures will not
     * be returned. If the cookie signature is present, but invalid, the cookie
     * will be deleted.
     *
     *     // Get the "theme" cookie, or use "blue" if the cookie does not exist
     *     $theme = Cookie::get('theme', 'blue');
     *
     * @param   string  $key        cookie name
     * @param   mixed   $default    default value to return
     * @return  string
     */
    public static function get( $key, $default = NULL )
    {
        if( ! isset( $_COOKIE[$key] ) )
        {
            // The cookie does not exist
            return $default;
        }

        // Get the cookie value
        $cookie = $_COOKIE[$key];

        // Find the position of the split between salt and contents
        $split = strlen( self::salt( $key, NULL ) );

        if( isset( $cookie[$split] ) && $cookie[$split] === '~' )
        {
            // Separate the salt and the value
            list( $hash, $value ) = explode( '~', $cookie, 2 );

            if( Security::slow_equals( self::salt( $key, $value ), $hash ) )
            {
                // Cookie signature is valid
                return $value;
            }

            // The cookie signature is invalid, delete it
            static::delete( $key );
        }

        return $default;
    }

    /**
     * Sets a signed cookie. Note that all cookie values must be strings and no
     * automatic serialization will be performed!
     *
     * [!!] By default, Cookie::$expiration is 0 - if you skip/pass NULL for the optional
     *      lifetime argument your cookies will expire immediately unless you have separately
     *      configured Cookie::$expiration.
     *
     *
     *     // Set the "theme" cookie
     *     Cookie::set('theme', 'red');
     *
     * @param   string  $name       name of cookie
     * @param   string  $value      value of cookie
     * @param   integer $lifetime   lifetime in seconds
     * @return  boolean
     * @uses    Cookie::salt
     */
    public static function set( $name, $value, $lifetime = NULL )
    {
        if( $lifetime === NULL )
        {
            // Use the default expiration
            $lifetime = self::$expiration;
        }

        if( $lifetime !== 0 )
        {
            // The expiration is expected to be a UNIX timestamp
            $lifetime += static::_time();
        }

        // Add the salt to the cookie value
        $value = self::salt( $name, $value ) . '~' . $value;

        return static::_setcookie( $name, $value, $lifetime, self::$path, self::$domain, self::$secure, self::$httponly );
    }

    /**
     * Deletes a cookie by making the value NULL and expiring it.
     *
     *     Cookie::delete('theme');
     *
     * @param   string  $name   cookie name
     * @return  boolean
     */
    public static function delete( $name )
    {
        // Remove the cookie
        unset( $_COOKIE[$name] );

        // Nullify the cookie and make it expire
        return static::_setcookie( $name, NULL, -86400, self::$path, self::$domain, self::$secure, self::$httponly );
    }

    /**
     * Generates a salt string for a cookie based on the name and value.
     *
     *     $salt = Cookie::salt('theme', 'red');
     *
     * @param   string $name name of cookie
     * @param   string $value value of cookie
     *
     * @throws Foundation_Exception if Cookie::$salt is not configured
     * @return  string
     */
    public static function salt( $name, $value )
    {
        // Require a valid salt
        if( ! self::$salt )
        {
            throw new Foundation_Exception(
                'A valid cookie salt is required. Please set Cookie::$salt in APPDIR/load/load.php.'
            );
        }

        // Determine the user agent
        $agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : 'unknown';

        return hash_hmac( 'sha1', $agent . $name . $value . self::$salt, self::$salt );
    }

    /**
     * Proxy for the native setcookie function - to allow mocking in unit tests so that they do not fail when headers
     * have been sent.
     *
     * @param string  $name
     * @param string  $value
     * @param integer $expire
     * @param string  $path
     * @param string  $domain
     * @param boolean $secure
     * @param boolean $httponly
     *
     * @return bool
     * @see setcookie
     */
    protected static function _setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly )
    {
        return setcookie( $name, $value, $expire, $path, $domain, $secure, $httponly );
    }

    /**
     * Proxy for the native time function - to allow mocking of time-related logic in unit tests
     *
     * @return int
     * @see    time
     */
    protected static function _time()
    {
        return time();
    }
}
