/**
 * Description of <?php echo $name ?>.
 *
<?php if ( ! empty($help)): ?>
 * <comment>Additional options:</comment>
 *
 *   <info>--option1=VALUE1</info>
 *
 *     Description of this option.
 *
 *   <info>--option2=VALUE2</info>
 *
 *     Description of this option.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion task --option1=value1</info>
 *
 *     Description of this example.
 *
 * <info>minion task --option1=value1 --option2=value2</info>
 *
 *     Description of this example.
 *
<?php endif; ?>
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
class <?php
	echo $name;
	echo ' extends ', ( ! empty($extends) ? $extends : 'Task_Generate');
	if ( ! empty($blank)) {echo ' {}';} else { ?> 
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name' => '',
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
	);

	/**
	 * Validates the task options.
	 *
	 * @param   Validation  $validation  The validation object to add rules to
	 * @return  Validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('name', 'not_empty');
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder(array $options)
	{
		$builder = Generator::build();

		return $builder;
	}

} // End <?php echo $name ?>
<?php } ?> 
