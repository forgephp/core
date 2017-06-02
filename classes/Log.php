<?php

namespace Forge;

/** 
 * Log
 * Message logging with observer-based log writing.
 *
 * [!!] This class does not support extensions, only additional writers.
 * 
 * @package    SuperFan
 * @category   Log
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Log
{
	// Log message levels - Windows users see PHP Bug #18090
	const EMERGENCY = LOG_EMERG;    // 0
	const ALERT     = LOG_ALERT;    // 1
	const CRITICAL  = LOG_CRIT;     // 2
	const ERROR     = LOG_ERR;      // 3
	const WARNING   = LOG_WARNING;  // 4
	const NOTICE    = LOG_NOTICE;   // 5
	const INFO      = LOG_INFO;     // 6
	const DEBUG     = LOG_DEBUG;    // 7
	const STRACE    = 8;

	// Numeric log level to string lookup table.
	protected static $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
		8           => 'STRACE',
	);

	// timestamp format for log entries
	public static $timestamp = 'Y-m-d H:i:s';

	// timezone for log entries
	public static $timezone;

	// immediately write when logs are added
	public static $write_on_add = FALSE;

	// Singleton instance container
	protected static $_instance;

	// list of added messages
	protected $_messages = array();

	// list of log writers
	protected $_writers = array();

	// Get the singleton instance of this class and enable writing at shutdown.
	public final static function instance()
	{
		if( self::$_instance === NULL )
		{
			// Create a new instance
			self::$_instance = new Log;

			// Write the logs at shutdown
			register_shutdown_function(array(Log::$_instance, 'write'));
		}

		return self::$_instance;
	}

	public static function level_name( $id )
	{
		return self::$_log_levels[$id];
	}

	// Attaches a log writer, and optionally limits the levels of messages that
	// will be written by the writer.
	public function attach( Log_Writer $writer, $levels = array(), $min_level = 0 )
	{
		if ( ! is_array( $levels ) )
		{
			$levels = range( $min_level, $levels );
		}
		
		$this->_writers["{$writer}"] = array(
			'object' => $writer,
			'levels' => $levels
		);

		return $this;
	}

	// Detaches a log writer. The same writer object must be used.
	public function detach( Log_Writer $writer )
	{
		// Remove the writer
		unset( $this->_writers["{$writer}"] );

		return $this;
	}

	// Adds a message to the log. Replacement values must be passed in to be
	// replaced using [strtr](http://php.net/strtr).
	public function add( $level, $message, array $values = NULL )
	{
		if( $values )
		{
			// Insert the values into the message
			$message = strtr( $message, $values );
		}

		// Create a new message and timestamp it
		$this->_messages[] = array(
			'time'  => Log::formatted_time('now', Log::$timestamp, Log::$timezone),
			'level' => $level,
			'body'  => $message,
		);

		if( Log::$write_on_add )
		{
			// Write logs as they are added
			$this->write();
		}

		return $this;
	}

	// Write and clear all of the messages.
	public function write()
	{
		if( empty( $this->_messages ) )
		{
			// There is nothing to write, move along
			return;
		}

		// Import all messages locally
		$messages = $this->_messages;

		// Reset the messages array
		$this->_messages = array();

		foreach( $this->_writers as $writer )
		{
			if( empty( $writer['levels'] ) )
			{
				// Write all of the messages
				$writer['object']->write( $messages );
			}
			else
			{
				// Filtered messages
				$filtered = array();

				foreach( $messages as $message )
				{
					if( in_array( $message['level'], $writer['levels'] ) )
					{
						// Writer accepts this kind of message
						$filtered[] = $message;
					}
				}

				// Write the filtered messages
				$writer['object']->write( $filtered );
			}
		}
	}

	// Returns a date/time string with the specified timestamp format
	public static function formatted_time( $datetime_str = 'now', $timestamp_format = 'Y-m-d H:i:s', $timezone = 'America/Chicago' )
	{
		$tz   = new \DateTimeZone( $timezone ? $timezone : date_default_timezone_get() );
		$time = new \DateTime( $datetime_str, $tz );

		// Convert the time back to the expected timezone if required (in case the datetime_str provided a timezone,
		// offset or unix timestamp. This also ensures that the timezone reported by the object is correct on HHVM
		// (see https://github.com/facebook/hhvm/issues/2302).
		$time->setTimeZone( $tz );

		return $time->format( $timestamp_format );
	}

}

// Log writer abstract class. All [Log] writers must extend this class.
abstract class Log_Writer
{
	// Numeric log level to string lookup table.
	protected $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
		8           => 'STRACE',
	);

	// Write an array of messages.
	abstract public function write( array $messages );

	// Allows the writer to have a unique key when stored.
	final public function __toString()
	{
		return spl_object_hash( $this );
	}

}
