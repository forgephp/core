<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * File-based configuration reader. Multiple configuration directories can be
 * used by attaching multiple instances of this class to Config.
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Config_File_Reader implements Config_Reader
{
    /**
     * The directory where config files are located
     * @var string
     */
    protected $_directory = '';

    /**
     * Creates a new file reader using the given directory as a config source
     *
     * @param string    $directory  Configuration directory to search
     */
    public function __construct( $directory = 'config' )
    {
        // Set the configuration directory name
        $this->_directory = trim( $directory, '/' );
    }

    /**
     * Load and merge all of the configuration files in this group.
     *
     *     $config->load($name);
     *
     * @param   string  $group  configuration group name
     * @return  $this   current object
     * @uses    Foundation::load
     */
    public function load( $group )
    {
        $config = array();

        if( $files = Foundation::find_file( $this->_directory, $group, NULL, TRUE ) )
        {
            foreach( $files as $file )
            {
                // Merge each file to the configuration array
                $config = Arr::merge( $config, Foundation::load( $file ) );
            }
        }

        return $config;
    }

}
