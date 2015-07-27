<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class WorkbenchCommand extends Command {
	protected $command = 'workbench';
	protected $description = 'Set up the workbench folder for a repository.';
	private $vagrant;

	protected function addArguments() {
		$this->makeArgument('repository')
			->setDescription('The repository for which the workbench folder will be set up.');
	}

	protected function addOptions() {}

	private function execCommand($command, $returnStatus = null) {
		$output = [];
		exec($command, $output, $returnStatus);
		return $output;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		// Install bower
		$process = new Process('which bower');
		$process->run();
		if (!$process->isSuccessful()) {
			$output->writeln('<info>Installing bower...</info>');
			$process = new Process('npm install -g bower');
			$process->run();
			if(!$process->isSuccessful()) {
				throw new RuntimeException('Failed to install bower.');
			}
		}

		// Get the root of the git repo
		$process = new Process('git rev-parse --show-toplevel');
		$process->run();
		if (!$process->isSuccessful()) {
			throw new RuntimeException($process->getErrorOutput());
		}

		$rootDirectory = trim($process->getOutput(), " \n\r");
		if (empty($rootDirectory)) {
			throw new RuntimeException('You are not currently inside a git repository.');
		}

		# Get information of the repository
		$repository = $input->getArgument('repository');
		$output->writeln(sprintf('<info>Gathering information for %s...</info>', $repository));
		$process = new Process(sprintf('composer info %s | grep "^source" | head -n 1 | awk \'{print $4}\'', $repository));
		$process->run();
		if (!$process->isSuccessful() || is_null($process->getOutput())) {
			throw new RuntimeException(sprintf('Failed getting information about the "%s" repository.', $repository));
		}

		$gitRepo = trim($process->getOutput(), " \n\r");

		if (strpos($gitRepo, 'https://github.com/') !== false) {
			$gitRepo = sprintf('git@github.com:%s', substr($gitRepo, 19));
		}

		# Create the workbench directory
		chdir($rootDirectory);
		if(!is_dir('workbench')) {
			mkdir('workbench', 0755);
		}
		chdir('workbench');

		$subDir = substr($repository, 0, strrpos($repository, '/'));
		if(!is_dir($subDir)) {
			mkdir($subDir, 0755);
		}
		chdir($subDir);

		$fileList = $this->execCommand('ls');

		# Clone the git repo
		$output->writeln(sprintf('<info>Cloning %s...</info>', $gitRepo));
		$process = new Process(sprintf('git clone %s', $gitRepo));
		$process->run();
		if (!$process->isSuccessful()) {
			throw new RuntimeException(sprintf('Failed cloning %s', $gitRepo));
		}

		$newFileList = $this->execCommand('ls');
		$repoDir = array_values(array_diff($newFileList, $fileList))[0];
		chdir($repoDir);

		$output->writeln('<info>Regenerating autoload files...</info>');
		$process = new Process(sprintf('php %s/artisan dump-autoload', $rootDirectory));
		$process->run();
		if (!$process->isSuccessful()) {
			throw new RuntimeException($process->getErrorOutput());
		}

		# Install bower dependencies
		$process = new Process('ls | grep "bower.json"');
		$process->run();
		if ($process->isSuccessful() && !empty($process->getOutput())) {
			$output->writeln('<info>Installing dependencies...</info>');
			$process = new Process('npm install && bower install');
			$process->run();
			if (!$process->isSuccessful()) {
				throw new RuntimeException('Failed installing the dependencies.');
			}
		}
	}
}