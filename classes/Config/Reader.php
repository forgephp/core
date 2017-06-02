<?php

namespace Forge\Config;

use Forge\Config\Source;

/**
 * Interface for config readers
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
interface Reader extends Source
{

    /**
     * Tries to load the specified configuration group
     *
     * Returns FALSE if group does not exist or an array if it does
     *
     * @param  string $group Configuration group
     * @return boolean|array
     */
    public function load( $group );
}
