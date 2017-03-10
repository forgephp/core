<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * DataLog Model
 *
 * @package    SuperFan
 * @category   Data Log
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Model_DataLog extends ORM
{
    protected $_table_name = 'datalog';

    protected $_table_columns = array(
        'id' => array('type' => 'int'),
        'date_and_time' => array('type' => 'string'),
        'table_name' => array('type' => 'string'),
        'column_name' => array('type' => 'string'),
        'row_pk' => array('type' => 'int'),
        'username' => array('type' => 'string'),
        'old_value' => array('type' => 'string', 'is_nullable' => TRUE),
        'new_value' => array('type' => 'string', 'is_nullable' => TRUE),
    );

    protected $_created_column = array(
        'column' => 'date_and_time',
        'format' => 'Y-m-d H:i:s'
    );

    /**
     * Updates or creates the record, setting the username to 'anonymous' (or
     * a localised equivalent) if the current user is not logged in with Auth.
     *
     * @chainable
     * @param  Validation $validation Validation object
     * @return Model_DataLog
     */
    public function save( Validation $validation = NULL )
    {
        $this->username = Auth::instance()->get_user();

        if( isset( $this->username->username ) )
        {
            $this->username = $this->username->username;
        }

        if( is_null( $this->username ) )
        {
            $this->username = __( Foundation::message( 'datalog', 'anon_username' ) );
        }

        return $this->loaded() ? $this->update( $validation ) : $this->create( $validation );
    }

}
