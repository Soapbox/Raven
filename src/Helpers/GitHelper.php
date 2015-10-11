<?php namespace SoapBox\Raven\Helpers;

use RuntimeException;
use Symfony\Component\Process\Process;

class GitHelper
{
	public static function getRepositoryRoot()
	{
		$process = new Process("git rev-parse --show-toplevel");
		$process->run();

		if ( !$process->isSuccessful() ||  $process->getOutput() == '') {
			throw new RuntimeException('You are not currently in a git repository.');
		}

		return trim($process->getOutput(), " \r\n");
	}
}
