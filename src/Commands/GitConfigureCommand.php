<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class GitConfigureCommand extends Command
{
	protected $command = 'git-configure';
	protected $description = 'Configure git hooks for the current repository.';

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$process = new Process("git pull");
		$process->run();

		$process = new Process("git rev-parse --show-toplevel");
		$process->run();
		
		if ( !$process->isSuccessful() ) {
			throw new RuntimeException('You are not currently in a git repository.');
		}

		$gitHookDir = $process->getOutput() . '/.git/hooks/';

		$files = glob(getRootDir() . '/git-hooks/*');

		foreach ($files as $file) {
			if (is_file($file)) {
				copy($file, $gitHookDir);
			}
		}
	}
}
