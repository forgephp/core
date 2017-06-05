<?php

namespace Forge\Bench;

/**
 * @package    SuperFan
 * @category   Codebench/Tests
 * @author     SuperFan Team <dev@superfanu.com>
 * @copyright  (c) 2016 - 2017 SuperFan, Inc.
 * @license    All rights reserved
 */
class Bench_LtrimDigits extends Codebench
{
	public $description = 'Chopping off leading digits: regex vs ltrim.';

	public $loops = 100000;

	public $subjects = array
	(
		'123digits',
		'no-digits',
	);

	public function bench_regex($subject)
	{
		return preg_replace('/^\d+/', '', $subject);
	}

	public function bench_ltrim($subject)
	{
		return ltrim($subject, '0..9');
	}
}