<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/** 
 * Foundation
 * Core Framework foundation for SuperFan
 *
 * "It pays to be obvious, especially if you have a reputation for subtlety."
 *     - Isaac Asimov, Foundation
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Foundation
{
    // Release version and codename
    const VERSION  = '6.0.0';
    const CODENAME = 'turf';

    // Common environment type constants for consistency and convenience
    const PRODUCTION  = 10;
    const STAGING     = 20;
    const TESTING     = 30;
    const DEVELOPMENT = 40;
    
    // Security check that is added to all generated PHP files
    const FILE_SECURITY = '<?php defined(\'BASE_DIR\') OR die(\'No direct script access.\');';
    
    // Format of cache files: header, cache name, and data
    const FILE_CACHE = ":header \n\n// :name\n\n:data\n";
    
    /**
     * @var  string  Current environment name
     */
    public static $environment = self::DEVELOPMENT;
    
    /**
     * @var  boolean  True if running on windows
     */
    public static $is_windows = FALSE;
    
    /**
     * @var  boolean  True if magic quotes (http://php.net/manual/en/security.magicquotes.php) is enabled.
     */
    public static $magic_quotes = FALSE;
    
    /**
     * @var  boolean  TRUE if PHP safe mode is on
     */
    public static $safe_mode = FALSE;

    /**
     * @var  string  base URL to the application
     */
    public static $base_url = '/';

    /**
     * @var  string  Application index file, added to generated links.
     */
    public static $index_file = NULL;

    /**
     * @var  string
     */
    public static $content_type = 'text/html';
    
    /**
     * @var  string  character set of input and output
     */
    public static $charset = 'utf-8';
    
    /**
     * @var  boolean  Whether to use internal caching for Foundation::find_file
     */
    public static $caching = FALSE;
    
    /**
     * @var  boolean  Enable Foundation catching and displaying PHP errors and exceptions.
     */
    public static $errors = TRUE;
    
    /**
     * @var  array  Types of errors to display at shutdown
     */
    public static $shutdown_errors = array( E_PARSE, E_ERROR, E_USER_ERROR );
    
    /**
     * @var  boolean  set the X-Powered-By header
     */
    public static $expose = TRUE;

    /**
     * @var  string   salt used in cookies
     */
    public static $salt = FALSE;
    
    /**
     * @var  Log  logging object
     */
    public static $log;
    
    /**
     * @var  Config  config object
     */
    public static $config;
    
    /**
     * @var  boolean  Has Foundation::init been called?
     */
    protected static $_init = FALSE;
    
    /**
     * @var  array   Currently active modules
     */
    protected static $_modules = array();
    
    /**
     * @var  array   Include paths that are used to find files
     */
    protected static $_paths = array( APPDIR, FOUNDATION );
    
    /**
     * @var  array   File path cache, used when caching is true
     */
    protected static $_files = array();
    
    /**
     * @var  boolean  Has the file path cache changed during this execution?  Used internally when when caching is true
     */
    protected static $_files_changed = FALSE;
    
    /**
     * Initializes the environment:
     *
     * - Disables register_globals and magic_quotes_gpc
     * - Determines the current environment
     * - Set global settings
     * - Sanitizes GET, POST, and COOKIE variables
     * - Converts GET, POST, and COOKIE variables to the global character set
     *
     * The following settings can be set:
     *
     * Type      | Setting    | Description                                    | Default Value
     * ----------|------------|------------------------------------------------|---------------
     * `string`  | charset    | Character set used for all input and output    | `"utf-8"`
     * `boolean` | errors     | Should Forge catch PHP errors and uncaught Exceptions and show the `error_view`.. <br /> <br /> Recommended setting: `TRUE` while developing, `FALSE` on production servers. | `TRUE`
     * `boolean` | caching    | Cache file locations to speed up [Forge::find_file].  <br /> <br />  Recommended setting: `FALSE` while developing, `TRUE` on production servers. | `FALSE`
     * `boolean` | expose     | Set the X-Powered-By header
     *
     * @throws  Foundation_Exception
     * @param   array   $settings   Array of settings.  See above.
     * @return  void
     * @uses    Foundation::globals
     * @uses    Foundation::sanitize
     * @uses    Foundation::cache
     */
    public static function start( array $settings=NULL )
    {
        if( self::$_init )
        {
            // Do not allow execution twice
            return;
        }

        // Foundation is now initialized
        self::$_init = TRUE;
        
        // Start an output buffer
        ob_start();

        if( isset( $settings['errors'] ) )
        {
            // Enable error handling
            self::$errors = (bool) $settings['errors'];
        }

        if( self::$errors === TRUE )
        {
            // Enable Foundation exception handling, adds stack traces and error source.
            set_exception_handler( array( 'Foundation_Exception', 'handler' ) );

            // Enable Foundation error handling, converts all PHP errors to exceptions.
            set_error_handler( array( 'Foundation', 'error_handler' ) );
        }

        // Enable xdebug parameter collection in development mode to improve fatal stack traces.
        if( self::$environment == self::DEVELOPMENT && extension_loaded('xdebug') )
        {
            ini_set( 'xdebug.collect_params', 3 );
        }

        // Enable the Foundation shutdown handler, which catches E_FATAL errors.
        register_shutdown_function( array( 'Foundation', 'shutdown_handler' ) );

        if( ini_get( 'register_globals' ) )
        {
            // Reverse the effects of register_globals
            self::globals();
        }

        if( isset( $settings['expose'] ) )
        {
            self::$expose = (bool) $settings['expose'];
        }

        // Determine if we are running in a Windows environment
        self::$is_windows = ( DIRECTORY_SEPARATOR === '\\' );

        // Determine if we are running in safe mode
        self::$safe_mode = (bool) ini_get( 'safe_mode' );

        if( isset( $settings['caching'] ) )
        {
            // Enable or disable internal caching
            self::$caching = (bool) $settings['caching'];
        }

        if( self::$caching === TRUE )
        {
            // Load the file path cache
            self::$_files = self::cache( 'Foundation::find_file()' );
        }

        if( isset( $settings['charset'] ) )
        {
            // Set the system character set
            self::$charset = strtolower( $settings['charset'] );
        }

        if( function_exists( 'mb_internal_encoding' ) )
        {
            // Set the MB extension encoding to the same character set
            mb_internal_encoding( self::$charset );
        }

        if( isset( $settings['base_url'] ) )
        {
            // Set the base URL
            self::$base_url = rtrim( $settings['base_url'], '/' ) . '/';
        }

        if( isset( $settings['index_file'] ) )
        {
            // Set the index file
            self::$index_file = trim( $settings['index_file'], '/' );
        }

        // Determine if the extremely evil magic quotes are enabled
        self::$magic_quotes = (bool) get_magic_quotes_gpc();

        // Sanitize all request variables
        $_GET    = self::sanitize( $_GET );
        $_POST   = self::sanitize( $_POST );
        $_COOKIE = self::sanitize( $_COOKIE );

        // Load the logger if one doesn't already exist
        if( ! self::$log instanceof Log )
        {
            self::$log = Log::instance();
        }

        // Load the config if one doesn't already exist
        if( ! self::$config instanceof Config )
        {
            self::$config = new Config;
        }
    }

    /**
     * Cleans up the environment:
     *
     * - Restore the previous error and exception handlers
     * - Destroy the Foundation::$log and Foundation::$config objects
     *
     * @return  void
     */
    public static function deinit()
    {
        if( self::$_init )
        {
            // Removed the autoloader
            spl_autoload_unregister( array( 'Foundation', 'auto_load' ) );

            if( self::$errors )
            {
                // Go back to the previous error handler
                restore_error_handler();

                // Go back to the previous exception handler
                restore_exception_handler();
            }

            // Destroy objects created by init
            self::$log = self::$config = NULL;

            // Reset internal storage
            self::$_files = array();

            // Reset file cache status
            self::$_files_changed = FALSE;

            // Foundation is no longer initialized
            self::$_init = FALSE;
        }
    }

    /**
     * Provides auto-loading support of classes that follow Forge's class
     * naming conventions.
     *
     *     // Loads classes/My/Class/Name.php
     *     Foundation::auto_load( 'My_Class_Name' );
     *
     * or with a custom directory:
     *
     *     // Loads vendor/My/Class/Name.php
     *     Foundation::auto_load( 'My_Class_Name', 'vendor' );
     *
     * You should never have to call this function, as simply calling a class
     * will cause it to be called.
     *
     * This function must be enabled as an autoloader in the bootstrap:
     *
     *     spl_autoload_register( array( 'Foundation', 'auto_load' ) );
     *
     * @param   string  $class      Class name
     * @param   string  $directory  Directory to load from
     * @return  boolean
     */
    public static function auto_load( $class, $directory='classes' )
    {
        // Transform the class name according to PSR-0
        $class = ltrim( $class, '\\' );
        $file = '';
        $namespace = '';

        if( $last_namespace_position = strripos( $class, '\\' ) )
        {
            $namespace = substr( $class, 0, $last_namespace_position );
            $class = substr( $class, $last_namespace_position + 1 );
            $file = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
        }

        $file .= str_replace( '_', DIRECTORY_SEPARATOR, $class );

        if( $path = self::find_file( $directory, $file ) )
        {
            // Load the class file
            require $path;

            // Class has been found
            return TRUE;
        }

        // Class is not in the filesystem
        return FALSE;
    }

    /**
     * Searches for a file in the filesystem, and returns the path to the file
     * that has the highest precedence, so that it can be included.
     *
     * When searching the "config", "messages", or "i18n" directories, or when
     * the `$array` flag is set to true, an array of all the files that match
     * that path in the filesystem will be returned. These files will return
     * arrays which must be merged together.
     *
     * If no extension is given, the default extension ('.php') will be used.
     *
     *     // Returns an absolute path to views/template.php
     *     Forge\Core\Forge::find_file( 'views', 'template' );
     *
     *     // Returns an absolute path to media/css/style.css
     *     Forge::find_file('media', 'css/style', 'css');
     *
     *     // Returns an array of all the "mimes" configuration files
     *     Forge::find_file('config', 'mimes');
     *
     * @param   string  $dir    directory name (views, i18n, classes, extensions, etc.)
     * @param   string  $file   filename with subdirectory
     * @param   string  $ext    extension to search for
     * @param   boolean $array  return an array of files?
     * @return  array   a list of files when $array is TRUE
     * @return  string  single file path
     */
    public static function find_file( $dir, $file, $ext=NULL, $array=FALSE )
    {
        if( $ext === NULL )
        {
            // Use the default extension
            $ext = '.php';
        }

        else if( $ext )
        {
            // Prefix the extension with a period
            $ext = ".{$ext}";
        }

        else
        {
            // Use no extension
            $ext = '';
        }

        // Create a partial path of the filename
        $path = $dir . DIRECTORY_SEPARATOR . $file . $ext;

        if( self::$caching === TRUE && isset( self::$_files[ $path . ( $array ? '_array' : '_path' ) ] ) )
        {
            // This path has been cached
            return self::$_files[ $path . ( $array ? '_array' : '_path' ) ];
        }

        if( $array || $dir === 'config' || $dir === 'i18n' || $dir === 'messages' )
        {
            // Include paths must be searched in reverse
            $paths = array_reverse( self::$_paths );

            // Array of files that have been found
            $found = array();

            foreach( $paths as $dir )
            {
                if( is_file( $dir . $path ) )
                {
                    // This path has a file, add it to the list
                    $found[] = $dir . $path;
                }
            }
        }

        else
        {
            // The file has not been found yet
            $found = FALSE;

            foreach( self::$_paths as $dir )
            {
                if( is_file( $dir . $path ) )
                {
                    // A path has been found
                    $found = $dir.$path;

                    // Stop searching
                    break;
                }
            }
        }

        if( self::$caching === TRUE )
        {
            // Add the path to the cache
            self::$_files[ $path . ( $array ? '_array' : '_path' ) ] = $found;

            // Files have been changed
            self::$_files_changed = TRUE;
        }

        return $found;
    }

    /**
     * Recursively finds all of the files in the specified directory at any
     * location in the Cascading Filesystem, and returns an
     * array of all the files found, sorted alphabetically.
     *
     *     // Find all view files.
     *     $views = Foundation::list_files('views');
     *
     * @param   string  $directory  directory name
     * @param   array   $paths      list of paths to search
     * @return  array
     */
    public static function list_files( $directory = NULL, array $paths = NULL )
    {
        if( $directory !== NULL )
        {
            // Add the directory separator
            $directory .= DIRECTORY_SEPARATOR;
        }

        if( $paths === NULL )
        {
            // Use the default paths
            $paths = self::$_paths;
        }

        // Create an array for the files
        $found = array();

        foreach( $paths as $path )
        {
            if( is_dir( $path . $directory ) )
            {
                // Create a new directory iterator
                $dir = new DirectoryIterator( $path . $directory );

                foreach( $dir as $file )
                {
                    // Get the file name
                    $filename = $file->getFilename();

                    if( $filename[0] === '.' || $filename[ strlen( $filename ) - 1 ] === '~' )
                    {
                        // Skip all hidden files and UNIX backup files
                        continue;
                    }

                    // Relative filename is the array key
                    $key = $directory . $filename;

                    if( $file->isDir() )
                    {
                        if( $sub_dir = self::list_files( $key, $paths ) )
                        {
                            if( isset( $found[$key] ) )
                            {
                                // Append the sub-directory list
                                $found[$key] += $sub_dir;
                            }
                            else
                            {
                                // Create a new sub-directory list
                                $found[$key] = $sub_dir;
                            }
                        }
                    }
                    else
                    {
                        if( ! isset( $found[$key] ) )
                        {
                            // Add new files to the list
                            $found[$key] = realpath( $file->getPathName() );
                        }
                    }
                }
            }
        }

        // Sort the results alphabetically
        ksort( $found );

        return $found;
    }

    /**
     * Loads a file within a totally empty scope and returns the output:
     *
     *     $foo = Foundation::load('foo.php');
     *
     * @param   string  $file
     * @return  mixed
     */
    public static function load( $file )
    {
        return include $file;
    }

    /**
     * Returns the the currently active include paths
     *
     * @return  array
     */
    public static function include_paths()
    {
        return self::$_paths;
    }

    /**
     * Reverts the effects of the `register_globals` PHP setting by unsetting
     * all global variables except for the default super globals (GPCS, etc),
     * which is a potential security hole.
     *
     * [ref-wikibooks]: http://en.wikibooks.org/wiki/PHP_Programming/Register_Globals
     *
     * @return  void
     */
    public static function globals()
    {
        if( isset( $_REQUEST['GLOBALS'] ) || isset( $_FILES['GLOBALS'] ) )
        {
            // Prevent malicious GLOBALS overload attack
            echo "Global variable overload attack detected! Request aborted.\n";

            // Exit with an error status
            exit(1);
        }

        // Get the variable names of all globals
        $global_variables = array_keys( $GLOBALS );

        // Remove the standard global variables from the list
        $global_variables = array_diff( $global_variables, array(
            '_COOKIE',
            '_ENV',
            '_GET',
            '_FILES',
            '_POST',
            '_REQUEST',
            '_SERVER',
            '_SESSION',
            'GLOBALS',
        ) );

        foreach( $global_variables as $name )
        {
            // Unset the global variable, effectively disabling register_globals
            unset( $GLOBALS[$name] );
        }
    }

    /**
     * Recursively sanitizes an input variable:
     *
     * - Strips slashes if magic quotes are enabled
     * - Normalizes all newlines to LF
     *
     * @param   mixed   $value  any variable
     * @return  mixed   sanitized variable
     */
    public static function sanitize( $value )
    {
        if( is_array( $value ) || is_object( $value ) )
        {
            foreach( $value as $key => $val )
            {
                // Recursively clean each value
                $value[$key] = self::sanitize( $val );
            }
        }
        else if( is_string( $value ) )
        {
            if( self::$magic_quotes )
            {
                // Remove slashes added by magic quotes
                $value = stripslashes( $value );
            }

            if( strpos( $value, "\r" ) !== FALSE )
            {
                // Standardize newlines
                $value = str_replace( array( "\r\n", "\r" ), "\n", $value );
            }
        }

        return $value;
    }

    /**
     * PHP error handler, converts all errors into ErrorExceptions. This handler
     * respects error_reporting settings.
     *
     * @throws  ErrorException
     * @return  TRUE
     */
    public static function error_handler( $code, $error, $file = NULL, $line = NULL )
    {
        if( error_reporting() & $code )
        {
            // This error is not suppressed by current error reporting settings
            // Convert the error into an ErrorException
            throw new ErrorException( $error, $code, 0, $file, $line );
        }

        // Do not execute the PHP error handler
        return TRUE;
    }

    /**
     * Catches errors that are not caught by the error handler, such as E_PARSE.
     *
     * @uses    Foundation_Exception::handler
     * @return  void
     */
    public static function shutdown_handler()
    {
        if( ! self::$_init)
        {
            // Do not execute when not active
            return;
        }

        try
        {
            if( self::$caching === TRUE && self::$_files_changed === TRUE )
            {
                // Write the file path cache
                self::cache( 'Foundation::find_file()', self::$_files );
            }
        }
        catch( Exception $e )
        {
            // Pass the exception to the handler
            Foundation_Exception::handler( $e );
        }

        if( self::$errors && $error = error_get_last() && in_array( $error['type'], self::$shutdown_errors ) )
        {
            // Clean the output buffer
            ob_get_level() && ob_clean();

            // Fake an exception for nice debugging
            Foundation_Exception::handler( new ErrorException( $error['message'], $error['type'], 0, $error['file'], $error['line'] ) );

            // Shutdown now to avoid a "death loop"
            exit(1);
        }
    }

    /**
     * Generates a version string based on the variables defined above.
     *
     * @return string
     */
    public static function version()
    {
        return 'SuperFan Foundation v' . self::VERSION . ' (' . self::CODENAME . ')';
    }

}