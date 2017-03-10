<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Persistant, Cookie based messages
 *
 * @package    SuperFan
 * @category   Flash
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Flash
{
    static $namespace = 'Flash';

    /**
     * @var Array
     */
    static $views = array(
        'error'   => 'flash/error',
        'success' => 'flash/success',
        'notice'  => 'flash/notice',
    );

    /**
     * Set a message
     *
     *
     * @param  String $type
     * @param  String|Array $message
     * @param  Array  $options
     * @return void
     */
    public static function set( $type, $message, Array $options = NULL )
    {
        $messages = self::get();

        $messages[] = array(
            'type'    => $type,
            'message' => $message,
            'options' => $options,
        );

        Session::instance()->set( self::$namespace, $messages );
    }

    /**
     * Retrieves messages
     *
     * If $type is given, only returns that type of message
     *
     * @param Mixed $type
     * @return Array
     */
    public static function get( $type = FALSE )
    {
        $messages = Session::instance()->get( self::$namespace );

        if( FALSE !== $type )
        {
            return array_filter( $messages, 
                function( $row ) use( $type ) {
                    return $row['type'] === $type;
                }
            );
        }

        return $messages;
    }

    /**
     * Retrieve messages only once
     * 
     *
     * @param  String $type
     * @return Array
     */
    public static function get_once( $type = FALSE )
    {
        $messages = self::get( $type );

        self::clear( $type );

        return $messages;
    }

    /**
     * Clear flash messages 
     *
     * If $type is given, only clears that type of message
     *
     * @param  Mixed $type
     * @return void
     */
    public static function clear( $type = FALSE )
    {
        if( FALSE !== $type )
        {
            $messages = self::get();
            $messages = array_filter( $messages,
                function($row) use($type) {
                    return $row['type'] !== $type;
                } 
            );

            Session::instance()->set( self::$namespace, $messages );

            return;
        }

        Session::instance()->delete( self::$namespace );
    }

    /**
     * Render messages
     *
     * If $type is given, only render that type
     *
     * @param  String  $type
     * @param  Boolean $only_once
     * @param  Boolean $echo
     * @return String
     */
    public static function render( $type = FALSE, $only_once = TRUE )
    {
        $messages = $only_once ? self::get_once( $type ) : self::get( $type );

        if( empty( $messages ) )
        {
            return '';
        }

        foreach( $messages as $i => $message )
        {
            if( is_array( $message['message'] ) )
            {
                list( $path, $file ) = $message['message'];

                $text = Foundation::message( $path, $file );
            }
            else
            {
                $text = $message['message'];
            }

            if( array_key_exists($message['type'], self::$views ) )
            {
                $message = View::factory( self::$views[$message['type']], 
                    array(
                        'text' => __( $text, $message['options'] ),
                        'type' => $message['type'],
                    ) 
                )->render();
            }
            else
            {
                $message = __( $text, $message['options'] );
            }

            $messages[$i] = $message;
        }

        return implode( "\n", $messages );
    }
}
