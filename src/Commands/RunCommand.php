<?php namespace SoapBox\Raven\Commands;

use SoapBox\Raven\Utils\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
	protected $command = 'run';
	protected $description = 'Run commands through the SoapBox machine via SSH';

	protected function addArguments() {
		$this->makeArgument('ssh-command')
			->setDescription('The command to pass through to the virtual machine.')
			->required();
	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		chRootDir();

		$command = $input->getArgument('ssh-command');

		$this->runCommand($command);
	}

	protected function setEnvironmentCommand()
	{
		if ($this->isWindows()) {
			return 'SET VAGRANT_DOTFILE_PATH=' . $_ENV['VAGRANT_DOTFILE_PATH'] . ' &&';
		}

		return 'VAGRANT_DOTFILE_PATH="' . $_ENV['VAGRANT_DOTFILE_PATH'] . '"';
	}

	protected function isWindows()
	{
		return strpos(strtoupper(PHP_OS), 'WIN') === 0;
	}

	protected function runCommand($command, &$returnStatus = null, $passthru = true) {
		$command = $this->setEnvironmentCommand() . ' vagrant ssh -c "' . $command . '"';

		if ($passthru) {
			passthru($command, $returnStatus);
			return [];
		}

		$output = [];
		exec($command . ' 2>&1', $output, $returnStatus);

		return $output;
	}
}
