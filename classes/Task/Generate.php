<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Generates application and module files from templates using different
 * configurable generators.
 *
 * Usage: minion <alert>generate:GENERATOR</alert> [arguments] [options]
 *
 * <comment>Common options for all generators:</comment>
 *
 *   <info>--help</info>       :  Show each generator's options and usage
 *   <info>--pretend</info>    :  Run the generator without making changes
 *   <info>--force</info>      :  Replace any existing files
 *   <info>--quiet</info>      :  Don't output any status messages
 *   <info>--inspect</info>    :  View the rendered output only
 *   <info>--no-ask</info>     :  Don't ask for any user input
 *   <info>--remove</info>     :  Delete files and empty directories
 *   <info>--verbose</info>    :  Show more information when running
 *   <info>--no-ansi</info>    :  Disable ANSI output, e.g. <alert>colors</alert>
 *
 *   <info>--template=VIEW</info>
 *
 *     The view to use instead of the default view template, stored in 
 *     the /views folder.
 *
 *   <info>--module=NAME|FOLDER</info>
 *
 *     Either a loaded module name as defined in the bootstrap, or a valid
 *     folder name under <alert>MODPATH</alert> in which to create the files instead of
 *     the default <alert>APPPATH</alert>.
 *
 *   <info>--config=GROUP</info>
 *
 *     The config group to use with this task instead of the default
 *     value 'generator'.
 *
 * @package    SuperFan
 * @category   Generator
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Task_Generate extends Minion_Task
{
	/**
	 * The task options that apply to all generator tasks
	 * @var array
	 */
	protected $_common_options = array(
		'pretend'  => FALSE,
		'force'    => FALSE,
		'quiet'    => FALSE,
		'inspect'  => FALSE,
		'no-ask'   => FALSE,
		'remove'   => FALSE,
		'verbose'  => FALSE,
		'no-ansi'  => FALSE,
		'name'     => '',
		'module'   => '',
		'template' => '',
		'config'   => '',
	);

	/**
	 * The default mapping of positional arguments to task options
	 * @var  array
	 */
	protected $_arguments = array(
		1 => 'name',
	);

	/**
	 * Instantiates the task, and merges common and task options.
	 *
	 * This is re-declared public here so we can handle different instances more
	 * easily without having to use the factory method.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Merge any task options with the common options
		$this->_options = array_merge( $this->_common_options, $this->_options );

		parent::__construct();
	}

	/**
	 * Sets the task options passed as parameters, and converts any positional
	 * arguments to options.
	 *
	 * Boolean parameters (i.e. switches without values) are handled here by
	 * toggling their associated options on.
	 *
	 * @param   array  $options  The task options to set
	 * @return  Minion_Task  This instance
	 */
	public function set_options( array $options )
	{
		// Convert any positional arguments to options
		$options = $this->convert_arguments( $options, $this->_arguments );

		foreach( $options as $key => $value )
		{
			if( $value === NULL && isset( $this->_options[$key] ) && $this->_options[$key] === FALSE )
			{
				// Switch on the boolean option
				$options[$key] = TRUE;
			}
		}

		return parent::set_options( $options );
	}

	/**
	 * Converts any positional arguments to option values depending on the given
	 * mapping (where the key is the argument position starting at 1, and the
	 * value is the option name), then removes them.
	 *
	 * @param   array  $options  The options to process
	 * @param   array  $mapping  The argument to option mappings
	 * @return  array  The options with converted arguments
	 */
	public function convert_arguments( array $options, array $mapping )
	{
		foreach( $mapping as $position => $name )
		{
			if( isset( $options[$position] ) )
			{
				// Set the option to the argument value
				$options[$name] = empty( $options[$name] ) ? $options[$position] : $options[$name];

				// Remove the argument for validation
				unset( $options[$position] );
			}
		}

		return $options;
	}

	/**
	 * Runs the current task with the given generator builder.
	 *
	 * By default, this method runs in interactive mode, requesting user
	 * confirmation before any destructive changes, allowing inspection, etc.
	 *
	 * @param   Generator_Builder  $builder  The builder to execute
	 * @param   array  $options  The task options to use
	 * @return  void
	 */
	public function run( Generator_Builder $builder, array $options )
	{
		if( $options['inspect'] )
		{
			// Output debug info for each generator item
			$i = 1;

			foreach( $builder->inspect() as $item )
			{
				$this->_write( '' );
				$this->_write( $this->_color( "[ File $i ] " . Debug::path( $item['file'] ), 'brown' ) );
				$this->_write( '' );
				$this->_write( $item['rendered'] );
				$i++;
			}

			return;
		}

		// Choose which command to run
		$command = $options['remove'] ? Generator::REMOVE : Generator::CREATE;

		if( ! $options['quiet'] && ! $options['pretend'] && ! $options['no-ask'] )
		{
			// Run once in pretend mode to get a list of expected actions,
			// and don't continue if there's nothing to do
			if( ! $this->run_command( $command, $builder->with_pretend( TRUE ) ) )
			{
				return;
			}

			$this->_write( '' );

			// Ask for user confirmation
			if( 'y' !== $this->_read( 'Do you want to continue?', array( 'y', 'n' ) ) )
			{
				return;
			}
		}

		// Run the chosen command on the generators
		$this->run_command( $command, $builder->with_pretend( $options['pretend'] ) );
	}

	/**
	 * Runs the given command on the generators and outputs the log, or only
	 * lists the expected actions if we're in pretend mode.
	 *
	 * @param   string   $command  The command to be run
	 * @param   Generator_Builder  $builder  The builder holding the generators
	 * @return  boolean  TRUE if some action has been logged
	 */
	public function run_command( $command, Generator_Builder $builder )
	{
		if( $builder->is_pretend() )
		{
			$this->_write( '' );
			$this->_write( 'The result of running this task will be:' );
		}

		// Track the logged messages
		$messages = array();
		$this->_write('');

		foreach( $builder->generators() as $generator )
		{
			$generator->$command();

			foreach( $generator->log() as $msg )
			{
				if( ( $this->_options['verbose'] && $msg != end( $messages ) ) || ! in_array( $msg, $messages ) )
				{
					// We only want unique messages, unless in verbose mode and
					// the last message isn't being repeated
					$this->_write_log( $msg['status'], Debug::path( $msg['item'] ) );

					$messages[] = $msg;
				}
			}
		}

		if( empty( $messages ) )
		{
			// No actions have been logged
			$this->_write( '    Nothing to do, no changes made.' );

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Convenience method for loading configuration values, optionally from
	 * a given config group or an absolute file path.
	 *
	 * @param   string  $path    Array path to the config values
	 * @param   string  $source  The config group or file to load
	 * @return  mixed  The config values or NULL
	 */
	public function get_config( $path, $source = NULL )
	{
		return Generator::get_config( $source, $path );
	}

	/**
	 * Returns a generator builder created with the given configuration options.
	 * This method should be implemented in full by child classes.
	 *
	 * @param   array  $options  The selected task options
	 * @return  boolean|Generator_Builder  FALSE if no builder is available
	 */
	public function get_builder( array $options )
	{
		return FALSE;
	}

	/**
	 * Parses a task command string, returning an array of arguments and
	 * option values, should be reversible by create_task_command().
	 *
	 * @param   string  $command  The task command
	 * @return  array   The parsed command arguments
	 */
	public function parse_task_command( $command )
	{
		$command = explode( ' ', trim( $command ), 2 );

		// Set the task name and initialize options
		$result['task'] = $command[0];
		$result['options'] = array();

		if( isset( $command[1] ) )
		{
			// Get the options arguments
			preg_match_all( "/--(?P<option>[\w\-]+)(=(?P<value>(['\"].*?['\"]|.*?)))?(?:\s|\-|$)/", $command[1], $matches, PREG_SET_ORDER );

			foreach( $matches as $match )
			{
				// Set the options with or without values
				$value = isset( $match['value'] ) ? trim( $match['value'], "\"'" ) : NULL;
				$result['options'][$match['option']] = $value;
			}
		}

		return $result;
	}

	/**
	 * Creates a task command string from an array of arguments, reversing
	 * the results of parse_task_command().
	 *
	 * @param   array   $args  The command arguments
	 * @return  string  The task command
	 */
	public function create_task_command( array $args )
	{
		$command = $args['task'];

		foreach( $args['options'] as $key => $value )
		{
			$value = ( preg_match( '@[\s\|\\\/]@', $value ) ) ? ( '"' . $value . '"' ) : $value;
			$command .= ' --' . $key . ( $value ? ( '=' . $value ) : '' );
		}

		return $command;
	}

	/**
	 * Execute the task with the specified set of options.
	 *
	 * The Minion_Task method is overridden here mainly to handle validation
	 * errors and command output differently.
	 *
	 * @return  void
	 */
	public function execute()
	{
		$options = $this->get_options();

		// Validate $options
		$validation = Validation::factory( $options );
		$validation = $this->build_validation( $validation );

		if( $this->_method != '_help' && ! $validation->check() )
		{
			$view = View::factory( 'generator/error/validation' )
				->set( 'task', Minion_Task::convert_class_to_task( $this ) )
				->set( 'errors', $validation->errors( $this->get_errors_file() ) )
			;

			$this->_write( $this->_apply_styles( $view ), FALSE );
		}
		else
		{
			// Finally, run the task
			$method = $this->_method;
			$this->_write( $this->{$method}( $options ), FALSE );
		}
	}

	/**
	 * Loads a builder and runs the task, or outputs the common help message
	 * by default.
	 *
	 * @param   array  $params  The current task parameters
	 * @return  void
	 */
	protected function _execute( array $params )
	{
		if( $builder = $this->get_builder( $params ) )
		{
			$this->run( $builder->prepare(), $params );

			return;
		}

		$this->_help( $params );
	}

	/**
	 * Writes a message to the console, with optional new line.
	 *
	 * @param   string   $text  The message to write
	 * @param   boolean  $eol   Should EOL be added?
	 * @return  void
	 */
	protected function _write( $text, $eol = TRUE )
	{
		if( $this->_options['quiet'] )
		{
			return;
		}

		echo $text, ( $eol ? PHP_EOL : '' );
	}

	/**
	 * Writes a formatted log message to the console.
	 *
	 * @param   string  $status  The status message
	 * @param   string  $item    The item affected
	 * @return  void
	 */
	protected function _write_log( $status, $item )
	{
		$color = in_array( $status, array( Generator::CREATE, Generator::REMOVE ) ) ? 'green' : 'red';

		$this->_write( $this->_color( sprintf( '%10s  %s', $status, $item ), $color ) );
	}

	/**
	 * Returns the given text with the correct color codes for a foreground and
	 * optionally a background color.
	 *
	 * @param   string  $text        The text to color
	 * @param   string  $foreground  The foreground color
	 * @param   string  $background  The background color
	 * @return  string  The color coded string
	 */
	protected function _color( $text, $foreground, $background = NULL )
	{
		if( $this->_options['no-ansi'] )
		{
			return $text;
		}

		return Minion_CLI::color( $text, $foreground, $background );
	}

	/**
	 * Reads user input from STDIN.
	 *
	 * @param   string  $text     Text to show user before waiting for input
	 * @param   array   $options  Array of options the user is shown
	 * @return  string  The user input
	 */
	protected function _read( $text, array $options = NULL )
	{
		return Minion_CLI::read( $text, $options );
	}

	/**
	 * Outputs the help message for the given generator task.
	 *
	 * A list of available generators will be appended to the help only if the
	 * current instance is the base generator task.
	 *
	 * @param   array  $params  The current task parameters
	 * @return  void
	 */
	protected function _help( array $params )
	{
		$refl = new ReflectionClass( $this );

		// Parse the doccomment for the class
		list( $description, $tags ) = $this->_parse_doccomment( $refl->getDocComment() );

		$arguments = '';

		if( is_subclass_of( $this, 'Task_Generate' ) )
		{
			// Add details of any positonal arguments
			$arguments = strtoupper( implode( ' ', $this->_arguments ) );
			$arguments = ( $arguments != '' ) ? ( $arguments . ' ') : $arguments;
		}

		// Create the usage info string
		$usage = 'minion ' . Minion_Task::convert_class_to_task( $this ) . ' '
			. $this->_color( $arguments, 'green' )
			. $this->_color( '[--option=value] [--option]', 'brown' );

		// Render the initial view
		$view = View::factory('generator/help/task')
			->set('description', $description)
			->set('tags', (array) $tags)
			->set('usage', $usage);

		$this->_write($this->_apply_styles($view));

		if ( ! is_subclass_of($this, 'Task_Generate'))
		{
			// Get the list of available generators
			$generators = $this->_compile_task_list(Foundation::list_files('classes/task/generate'));

			// Append the list to the help output
			$this->_write($this->_color('Available generators:', 'brown'));
			$this->_write('');
			foreach ($generators as $generator)
			{
				$this->_write('  * '.$generator);
			}
			$this->_write('');
		}
	}

	/**
	 * Substitutes any style tags in the text with the defined styles.
	 *
	 * @param   string  $text  The text with style tags
	 * @return  string  The text with styles applied
	 */
	protected function _apply_styles($text)
	{
		$text = preg_replace('@<comment>(.*?)</comment>@s', $this->_color('$1', 'brown'), $text);
		$text = preg_replace('@<info>(.*?)</info>@s', $this->_color('$1', 'green'), $text);
		$text = preg_replace('@<alert>(.*?)</alert>@s', $this->_color('$1', 'red'), $text);

		return $text;
	}

} // End Generator_Task_Generate
