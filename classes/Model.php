<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Model base class. All models should extend this class.
 *
 * @package    SuperFan
 * @category   ORM
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
abstract class Model
{
    /**
     * Create a new model instance.
     *
     *     $model = Model::factory($name);
     *
     * @param   string  $name   model name
     * @return  Model
     */
    public static function factory( $name )
    {
        // Add the model prefix
        $class = 'Model_' . $name;

        return new $class;
    }

}
