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

	protected function addOptions() {
		$this->makeOption('new-only')
			->setDescription('Only install the hooks that do not exist.')
			->boolean();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<info>Configuring git hooks...</info>');

		$process = new Process("git pull");
		$process->run();

		$process = new Process("git rev-parse --show-toplevel");
		$process->run();
		
		if ( !$process->isSuccessful() ) {
			throw new RuntimeException('You are not currently in a git repository.');
		}

		$gitHookDir = trim($process->getOutput(), " \n\r") . '/.git/hooks/';
		$files = glob(getRootDir() . '/scripts/git-hooks/*');

		foreach ($files as $file) {
			if (is_file($file)) {
				$newFile = $gitHookDir . substr($file, strrpos($file, '/') + 1);
				if($input->getOption('new-only') && file_exists($newFile)) {
					continue;
				}
				copy($file, $newFile);
			}
		}

		$output->writeln('<info>Completed.</info>');
	}
}
