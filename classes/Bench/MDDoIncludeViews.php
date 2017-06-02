<?php

namespace Forge\Bench;

/**
 * @package    SuperFan
 * @category   Codebench/Tests
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Bench_MDDoIncludeViews extends Codebench
{
	public $description =
		'Optimization for the <code>doIncludeViews()</code> method of <code>Kohana_Kodoc_Markdown</code>
		 for the Kohana Userguide.';

	public $loops = 10000;

	public $subjects = array
	(
		// Valid matches
		'{{one}} two {{three}}',
		'{{userguide/examples/hello_world_error}}',

		// Invalid matches
		'{}',
		'{{}}',
		'{{userguide/examples/hello_world_error}',
		'{{userguide/examples/hello_world_error }}',
		'{{userguide/examples/{{hello_world_error }}',
	);

	public function bench_original($subject)
	{
		preg_match_all('/{{(\S+?)}}/m', $subject, $matches, PREG_SET_ORDER);
		return $matches;
	}

	public function bench_possessive($subject)
	{
		// Using a possessive character class
		// Removed useless /m modifier
		preg_match_all('/{{([^\s{}]++)}}/', $subject, $matches, PREG_SET_ORDER);
		return $matches;
	}

	public function bench_lookaround($subject)
	{
		// Using lookaround to move $mathes[1] into $matches[0]
		preg_match_all('/(?<={{)[^\s{}]++(?=}})/', $subject, $matches, PREG_SET_ORDER);
		return $matches;
	}

}