<?php

namespace Forge\Task;

use Forge\Arr;
use Forge\Debug;
use Forge\Foundation;
use Forge\Minion\CLI;
use Forge\Minion\Task;

/**
 * Help task to display general instructons and list all tasks
 *
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Plugin extends Task
{
    const SEARCH_OFFSET = 4;

    protected $_options = array(
        'install' => FALSE,
        'search'  => FALSE,
        'list'    => FALSE,
    );

    /**
     * Generates a help list for all tasks
     *
     * @return null
     */
    protected function _execute( array $params )
    {
        // load the default packages
        $packages = json_decode( file_get_contents( \FORGE\FOUNDATION . 'packages.json' ), true, 512, JSON_OBJECT_AS_ARRAY );

        // load any custom package configurations
        if( file_exists( \FORGE\APP . 'packages.json' ) )
        {
            $custom = json_decode( file_get_contents( \FORGE\APP . 'packages.json' ), true, 512, JSON_OBJECT_AS_ARRAY );

            $packages = Arr::merge( $custom, $packages );
        }

        // install
        if( $params['install'] )
        {
            if( isset( $packages[ $params['install'] ] ) )
            {
                $path = \FORGE\BASE . 'packages/' . $params['install'];

                CLI::write( 'Installing Package: ' . $params['install'] . ' to ' . $path );

                CLI::write( shell_exec( '/usr/bin/git clone --depth=1 ' . $packages[ $params['install'] ]['url'] . ' ' . $path . ' 2>&1' ) );

                CLI::write( 'Adding package to installed.json' );

                // load any custom package configurations
                if( file_exists( \FORGE\APP . 'installed.json' ) )
                {
                    $installed = json_decode( file_get_contents( \FORGE\APP . 'installed.json' ), true );
                }
                else
                {
                    $installed = array();
                }

                if( ! isset( $installed[ $params['install'] ] ) )
                {
                    $installed[ $params['install'] ] = $path;

                    // write to installed.json
                    file_put_contents( \FORGE\APP . 'installed.json', json_encode( $installed ) );

                    CLI::write( 'Installation Complete!' );
                }
            }
            else
            {
                CLI::write( 'Invalid Package Name!' );
            }
        }

        // search
        if( $params['search'] )
        {
            $len = max( array_map( 'strlen', array_keys( $packages ) ) ) + self::SEARCH_OFFSET;
            
            foreach( $packages as $key => $package )
            {
                if( stristr( $package['description'], $params['search'] ) !== FALSE || stristr( $key, $params['search'] ) !== FALSE )
                {
                    CLI::write( str_pad( $key, $len) . $package['description'] );
                }
            }
        }

        // list
        if( $params['list'] !== FALSE )
        {
            $len = max( array_map( 'strlen', array_keys( $packages ) ) ) + self::SEARCH_OFFSET;
            
            foreach( $packages as $key => $package )
            {
                CLI::write( str_pad( $key, $len ) . $package['description'] );
            }
        }
    }
}
