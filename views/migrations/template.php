<?php echo '<?php defined( \'FOUNDATION\' ) or die( \'No direct script access.\' );'; ?>


class <?php echo $migration_name; ?> extends Migration
{
    public function up()
    {
        // create table
        $this->create_table(
            '<?php echo $migration_name; ?>',
            array(
                'title' => array( 'string[64]' ),
                'created_by' => array( 'integer', 'unsigned' => true ),
                'created_on' => array( 'datetime' ),
                'row_status' => array( 'boolean', 'default' => false )
            )
        );
        
        // add column
        // $this->add_column( '<?php echo $migration_name; ?>', 'column_name', array('datetime', 'default' => NULL));
        
        // insert test data
        if( Foundation::$environment === Foundation::DEVELOPMENT )
        {
            DB::insert( '<?php echo $migration_name; ?>', array( 'title', 'created_on', 'created_by', 'row_status' ) )
                ->values( array(
                     'example', DB::expr( 'CURRENT_TIMESTAMP' ), '0', '1'
                ) )
                ->execute()
            ;
        }
    }

    public function down()
    {
        $this->drop_table( '<?php echo $migration_name; ?>' );
        
        // $this->remove_column( '<?php echo $migration_name; ?>', 'column_name' );
    }
}
