<?php

namespace Forge\Task\Queue;

use Forge\Minion\Task;

/**
 * Tools for processing queued jobs
 *
 * Accepts the following options:
 *  - queue: [default] 
 *  - count: [0]
 *  - sleep: [5]
 *
 *  Examples:
 *  Process Jobs with default settings
 *  $ ./minion jobs:work
 *
 *  Process jobs from the email queue
 *  $ ./minion jobs:work --queue=work
 *
 *  Change the number of jobs to process before exiting
 *  $ ./minion jobs:work --count=10
 *
 *  Change the amount of time to sleep between jobs
 *  $ /.minion jobs:work --sleep=10
 *
 * @package    SuperFan
 * @category   Queue
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Worker extends Task
{
    protected $_options = array(
        'queue'        => 'default',
        'count'        => 0,
        'sleep'        => 5,
        'verbose'      => FALSE,
        'pid'          => NULL,
        'max_attempts' => 5,
        'debug'        => FALSE,
    );

    public function _execute( Array $params )    
    {
        $worker = new Queue_Worker( $params );
        
        $worker->start();
    }
}