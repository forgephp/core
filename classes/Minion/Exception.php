<?php

namespace Forge\Minion;

use Forge\Exception as Forge_Exception;

/** 
 * Minion Exception
 * 
 * @package    SuperFan
 * @category   Minion
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Exception extends Forge_Exception
{
	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * Should this display a stack trace? It's useful.
	 *
	 * @uses    Foundation_Exception::text
	 * @param   Exception   $e
	 * @return  boolean
	 */
	public static function handler( \Exception $e )
	{
		try
		{
			// Log the exception
			Forge_Exception::log( $e );

			if( $e instanceof Exception )
			{
				echo $e->format_for_cli();
			}
			else
			{
				echo Forge_Exception::text( $e );
			}

			$exit_code = $e->getCode();

			// Never exit "0" after an exception.
			if( $exit_code == 0 )
			{
				$exit_code = 1;
			}

			exit( $exit_code );
		}
		catch( \Exception $e )
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Display the exception text
			echo Forge_Exception::text( $e ), "\n";

			// Exit with an error status
			exit(1);
		}
	}

	public function format_for_cli()
	{
		return Forge_Exception::text( $this );
	}
}
