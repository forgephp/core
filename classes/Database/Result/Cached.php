<?php

namespace Forge\Database\Query\Result;

use Forge\Database\Result;

/**
 * Object used for caching the results of select queries.
 *
 * @package    SuperFan
 * @category   Database
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Cached extends Result
{
	public function __construct( array $result, $sql, $as_object = NULL )
	{
		parent::__construct( $result, $sql, $as_object );

		// Find the number of rows in the result
		$this->_total_rows = count( $result );
	}

	public function __destruct()
	{
		// Cached results do not use resources
	}

	public function cached()
	{
		return $this;
	}

	public function seek( $offset )
	{
		if( $this->offsetExists( $offset ) )
		{
			$this->_current_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function current()
	{
		// Return an array of the row
		return $this->valid() ? $this->_result[$this->_current_row] : NULL;
	}
}
