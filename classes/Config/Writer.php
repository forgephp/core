<?php

namespace Forge\Config;

/**
 * Interface for config writers
 *
 * Specifies the methods that a config writer must implement
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
interface Config_Writer extends Config_Source
{
    /**
     * Writes the passed config for $group
     *
     * Returns chainable instance on success or throws
     * Foundation_Exception on failure
     *
     * @param string      $group  The config group
     * @param string      $key    The config key to write to
     * @param array       $config The configuration to write
     * @return boolean
     */
    public function write( $group, $key, $config );
}
