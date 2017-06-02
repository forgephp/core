<?php

namespace Forge\HTTP;

use Forge\HTTP\Message;

/**
 * A HTTP Response specific interface that adds the methods required
 * by HTTP responses. Over and above HTTP_Interaction, this
 * interface provides status.
 *
 * @package    SuperFan
 * @category   APP
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
interface Response extends Message
{
    /**
     * Sets or gets the HTTP status from this response.
     *
     *      // Set the HTTP status to 404 Not Found
     *      $response = Response::factory()
     *              ->status(404);
     *
     *      // Get the current status
     *      $status = $response->status();
     *
     * @param   integer  $code  Status to set to this response
     * @return  mixed
     */
    public function status( $code=NULL );
}
