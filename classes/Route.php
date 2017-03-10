<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Route
 *
 * @package    SuperFan
 * @category   APP
 * @author     Chris Nowak <chris@superfanu.com>
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Route
{
	// Matches a URI group and captures the contents
	const REGEX_GROUP   = '\(((?:(?>[^()]+)|(?R))*)\)';

	// Defines the pattern of a <segment>
	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

	// What can be part of a <segment> value
	const REGEX_SEGMENT = '[^/.,;?\n]++';

	// What must be escaped in the route regex
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';

	protected static $routes = array();

	protected static $current = FALSE;

	// list of valid localhost entries
	public static $localhosts = array( FALSE, '', 'local', 'localhost' );

	protected $_types = array();
	
	protected $_exec = FALSE;
	
	protected $_url = NULL;
	
	protected $_vars = array();
	
	protected $_defaults = array();
	
	protected $_filter = array();

	protected $_regex;

	public static function get( $url, $exec = FALSE )
	{
		return self::$routes[] = new Route( 'GET', $url, $exec );
	}

	public static function post( $url, $exec = FALSE )
	{
		return self::$routes[] = new Route( 'POST', $url, $exec );
	}

	public static function put( $url, $exec = FALSE )
    {
        return self::$routes[] = new Route( 'PUT', $url, $exec );
    }

    public static function patch( $url, $exec = FALSE )
    {
        return self::$routes[] = new Route( 'PATCH', $url, $exec );
    }

    public static function delete( $url, $exec = FALSE )
    {
        return self::$routes[] = new Route( 'DELETE', $url, $exec );
    }

    public static function match( $methods, $url, $exec = FALSE )
    {
        return self::$routes[] = new Route( $methods, $url, $exec );
    }

    public static function current()
    {
    	return self::$current;
    }

    public static function run( $route_file = FALSE )
	{
		// find the passed route file
		if( $route_file && ( $file = Foundation::find_file( 'routes', $route_file, NULL, TRUE ) ) )
		{
			// load the route files
			foreach( $file as $f )
			{
				Foundation::load( $f );
			}
		}
	}

	public static function all()
	{
		return self::$routes;
	}

	public static function find( $uri = FALSE )
	{
		if( ! $uri )
		{
			$url = '/';
		}

		foreach( $routes[$_SERVER['REQUEST_METHOD']] as $route => $rt )
		{
			preg_match( $route, $url . '/', $matches );

			if( $matches )
			{
				foreach( $matches as $key => $match )
				{
					if( $key > 0 )
					{
						$var[$rt['vars'][($key-1)]] = $match;
					}
				}

				$rt['vars'] = $var;

				return $rt;
			}
		}

		throw new HTTP_Exception( '404', 'No route matches: :method -> :url', array( ':method' => $_SERVER['REQUEST_METHOD'], ':url' => $url ) );
	}
	
	//this will execute whatever code you want just before it executes the route code
	//this is especially useful when you want certain code executed every time, no matter
	//which page, function, or class is called.
	//it will only execute once per page load.
	public static function execute( $cmd )
	{
		$class = @explode( '->', $cmd );

		if( class_exists( $class[0] ) )
		{
		    $return = new $class[0];
		    $return->$class[1]();
		}
		else if( is_callable( $cmd ) )
		{
			//it's a real or anonymous function
			$cmd();
		}
		else if( strpos( $cmd, '.php' ) )
		{
		    //it's a php page
		    global $vars;

		    if( file_exists( 'pages/' . $cmd ) )
		    {
				include( 'pages/' . $cmd );
			}
			else if( file_exists( $cmd ) )
			{
		    	include( $cmd );
		    }
		    else
		    {
			    throw new HTTP_Exception( '404', 'Sorry, I could not find: :cmd', array( ':cmd' => $cmd ) );
		    }
		}
		else
		{
			throw new HTTP_Exception( '400', 'Sorry, but I\'m not sure what to do with: :cmd', array( ':cmd' => $cmd ) );
		}
	}
	
	public static function redirect( string $cmd )
	{
		return HTTP::redirect( $cmd, 302 );
	}
	
	public static function checkHTTPS()
	{
		if( $_SERVER['HTTP_HOST'] == 'app.superfanu.com' )
		{
			if( $_SERVER['HTTPS']=='on' )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}
	
	public static function linkToHTTPS()
	{
		return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	
	public static function forceHTTPS()
	{
		if( $_SERVER['HTTP_HOST'] == 'app.superfanu.com' )
		{
			if( $_POST )
			{
				throw new HTTP_Exception( '405', 'You cannot redirect because there is a POST request.' );
			}

			if( $_SERVER['HTTPS'] != 'on' )
			{
				self::redirect( str_replace( 'http://', 'https://', $_SERVER['REDIRECT_SCRIPT_URI'] ) );
			}
		}
	}

	/**
	 * Returns the compiled regular expression for the route. This translates
	 * keys and optional groups to a proper PCRE regular expression.
	 *
	 *     $compiled = Route::compile(
	 *        '<controller>(/<action>(/<id>))',
	 *         array(
	 *           'controller' => '[a-z]+',
	 *           'id' => '\d+',
	 *         )
	 *     );
	 *
	 * @return  string
	 * @uses    Route::REGEX_ESCAPE
	 * @uses    Route::REGEX_SEGMENT
	 */
	public static function compile( $uri, array $filters = NULL )
	{
		// The URI should be considered literal except for keys and optional parts
		// Escape everything preg_quote would escape except for : ( ) < >
		$expression = preg_replace( '#' . self::REGEX_ESCAPE . '#', '\\\\$0', $uri );

		if( strpos( $expression, '(' ) !== FALSE )
		{
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace( array( '(', ')' ), array( '(?:', ')?' ), $expression );
		}

		// Insert default regex for keys
		$expression = str_replace( array( '<', '>' ), array( '(?P<', '>' . self::REGEX_SEGMENT . ')'), $expression );

		if( $filters )
		{
			$search = $replace = array();

			foreach( $filters as $key => $value )
			{
				$search[]  = "<$key>" . self::REGEX_SEGMENT;
				$replace[] = "<$key>$value";
			}

			// Replace the default regex with the user-specified regex
			$expression = str_replace( $search, $replace, $expression );
		}

		return '#^' . $expression . '$#uD';
	}

	public function __construct( $type, $url, $exec = FALSE )
	{
		$url = preg_replace('/(@[a-z]\w+)/', '<$1>', $url );
		$url = str_replace( '@', '', $url );
		$url = trim( $url, '/' );

		if( $exec instanceof Closure )
		{
			$this->_exec = $exec;
		}
		else if( is_array( $exec ) )
		{
			// is shorthand for a filter array
			$this->_filter = $exec;
		}
		
		foreach( (array) $type as $t )
		{
			$this->_types[] = strtoupper( $t );
		}

		$this->_url = $url;

		// compile the regex for faster routing
		$this->_regex = self::compile( $this->_url, $this->_filter );
	}

	public function defaults( array $array = array() )
	{
		$this->_defaults = $array;
	}

	public function filter( array $array = array() )
	{
		$this->_filter = $array;

		// recompile
		$this->_regex = self::compile( $this->_url, $this->_filter );
	}

	/**
	 * Returns whether this route is an external route
	 * to a remote controller.
	 *
	 * @return  boolean
	 */
	public function is_external()
	{
		return ! in_array( Arr::get( $this->_defaults, 'host', FALSE ), self::$localhosts );
	}

	public function matches( Request $request, $method = 'GET' )
	{
		// Get the URI from the Request
		$uri = trim( $request->uri(), '/' );

		if( ! preg_match( $this->_regex, $uri, $matches ) )
		{
			return FALSE;
		}

		// filter by Request::method()
		if( ! in_array( $method, $this->_types ) )
		{
			return FALSE;
		}

		// parse variable matches
		foreach( $matches as $key => $value )
		{
			if( is_int( $key ) )
			{
				// Skip all unnamed keys
				continue;
			}

			// Set the value for all matched keys
			$params[$key] = $value;
		}

		// load defaults
		foreach( $this->_defaults as $key => $value )
		{
			if( ! isset( $params[$key] ) || $params[$key] === '' )
			{
				// Set default values for any key that was not matched
				$params[$key] = $value;
			}
		}

		// pass the closure on
		$params['exec'] = $this->_exec;

		// format controller
		if( ! empty( $params['controller'] ) )
		{
			// PSR-0: Replace underscores with spaces, run ucwords, then replace underscore
			$params['controller'] = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $params['controller'] ) ) );
		}

		if( ! empty($params['directory'] ) )
		{
			// PSR-0: Replace underscores with spaces, run ucwords, then replace underscore
			$params['directory'] = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $params['directory'] ) ) );
		}

		return $params;
	}

}
