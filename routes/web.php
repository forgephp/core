<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

if( Foundation::$environment == Foundation::DEVELOPMENT )
{
    // database migrations
    Route::get( 'migrations' )
        ->defaults( array(
            'controller' => 'migrations',
        ) )
    ;

    Route::get( 'migrations/new' )
        ->defaults( array(
            'controller' => 'migrations',
            'action'     => 'new',
        ) )
    ;

    Route::post( 'migrations/create' )
        ->defaults( array(
            'controller' => 'migrations',
            'action'     => 'create',
        ) )
    ;

    Route::get( 'migrations/migrate' )
        ->defaults( array(
            'controller' => 'migrations',
            'action'     => 'migrate',
        ) )
    ;

    Route::get( 'migrations/rollback' )
        ->defaults( array(
            'controller' => 'migrations',
            'action'     => 'rollback',
        ) )
    ;
    
    // logreader media
    Route::get( 'logreader/media(/<file>)', array('file' => '.+'))
        ->defaults( array(
            'controller' => 'LogReader',
            'action'     => 'media',
        ) )
    ;

    // Set route to the LogReader API
    Route::get( 'logreader/api(/<action>)')
        ->defaults( array(
            'controller' => 'LogReaderAPI',
            'action' => 'index'
        ) )
    ;

    // Set route to the LogReader interface to a specific message
    Route::get( 'logreader/message/<message>', array('message' => '[0-9]+'))
        ->defaults( array(
            'controller' => 'LogReader',
            'action' => 'message'
        ) )
    ;

    // Set route to the LogReader interface
    Route::get( 'logreader(/<action>)', array('action' => 'about|index'))
        ->defaults( array(
            'controller' => 'LogReader',
            'action' => 'index'
        ) )
    ;

    // datalog
    Route::get( 'datalog/<table_name>(/<row_pk>)' )
        ->defaults( array(
            'controller' => 'DataLog',
            'action'     => 'index',
        ) )
    ;

    // Catch-all route for Codebench classes to run
    Route::match( ['get','post'], 'codebench(/<class>)' )
        ->defaults( array(
            'controller' => 'Codebench',
            'action' => 'index',
        ) )
    ;

    // queue
    Route::get( 'queue(/<action>(/<id>))' )
        ->defaults( array(
            'controller' => 'queue',
            'action'     => 'index',
        ) )
    ;

    // unit test

}
