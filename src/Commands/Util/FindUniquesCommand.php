<?php namespace SoapBox\Raven\Commands\Util;

class FindUniquesCommand extends FindDuplicatesCommand {
	protected $command = 'uniques';
	protected $description = 'Find unique lines in a file';
	protected $defaultFormat = '{line}';

	/**
	 * This method determines whether or not the current line should be written to
	 * the output.
	 *
	 * @param string $line The current line read from the input file
	 * @return bool Whether or not the line should be written to output
	 */
	protected function shouldWrite($line) {
		return !parent::shouldWrite($line);
	}
}