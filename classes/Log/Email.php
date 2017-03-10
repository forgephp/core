<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/** 
 * Log Email
 * a Log Writer that sends log messages and user information using email.
 * 
 * @package    SuperFan
 * @category   Log
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Log_Email extends Log_Writer
{
	protected $subject;
	protected $to;
	protected $from;

	public function __construct( $email_subject, $email_to, $email_from )
	{
		$this->subject = $email_subject;
		$this->to = (array) $email_to;
		$this->from = $email_from;
	}

	// Generates the email message and calls send()
	public function write( array $messages )
	{
		$email = '<h1>Log Report</h1>';
		
		foreach( $messages as $message )
		{
			foreach( $message as $title => $body )
			{	
				if( $title === 'level' )
				{
					$body = $this->_log_levels[$body];
				}
				
				$email .= '<h2>' . ucfirst( $title ) . '</h2><p>' . $body . '</p>';
			}
			
			$email .= '<br />';
		}
		
		$this->send( $email );
	}
	
	// Send email messages based on information found in the configuration file.
	private function send( $content )
	{
		require_once( BASE_DIR . 'classes/email.class.php' );

		foreach( $this->to as $email )
		{
			$mailer = new Email();

			$mailer->send( $email, $this->from, $this->subject, $content );
		}
	}
}
