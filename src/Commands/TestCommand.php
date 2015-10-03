<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends RunCommand {
	protected $command = 'test';
	protected $description = 'Run the testsuites.';
	private $vagrant;

	public function isEnabled() {
		return false;
	}

	protected function addArguments() {}

	protected function addOptions() {
		$this->makeOption('all')
			->addShortcut('a')
			->setDescription('Run all testsuites.')
			->boolean();

		$this->makeOption('unit')
			->addShortcut('u')
			->setDescription('Run the unit testsuite.')
			->boolean();

		$this->makeOption('integration')
			->addShortcut('i')
			->setDescription('Run the integration testsuite.')
			->boolean();

		$this->makeOption('permission')
			->addShortcut('p')
			->setDescription('Run the permission testsuite.')
			->boolean();

		$this->makeOption('filter')
			->addShortcut('f')
			->setDescription('Run only the tests that meet the supplied filters.')
			->isArray();

		$this->makeOption('testsuite')
			->addShortcut('t')
			->setDescription('Run only the supplied testsuites.')
			->isArray();

		$this->makeOption('no-refresh')
			->setDescription('Do not refresh the database for integration tests.')
			->boolean();

		$this->makeOption('vagrant')
			->setDescription('Run the tests from within the vagrant box.');
	}

	private function runTest($command) {
		$returnValue = 0;

		if ($this->vagrant) {
			$this->runCommand($command, $returnValue);
		} else {
			passthru($command, $returnValue);
		}

		return $returnValue;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$exitStatus = 0;

		$this->vagrant = $input->getOption('vagrant');

		if($input->getOption('unit') || $input->getOption('all')) {
			$output->writeln('<info>Running unit tests...</info>');
			$exitStatus |= $this->runTest('phpunit --testsuite unit');
		}

		if($input->getOption('integration') || $input->getOption('all')) {
			if ( !$input->getOption('no-refresh') ) {
				$refreshCommand = new RefreshCommand();
				$refreshCommand->execute($input, $output);
			}
			$output->writeln('<info>Running integration tests...</info>');
			$exitStatus |= $this->runTest('phpunit --testsuite integration');
		}

		if($input->getOption('permission') || $input->getOption('all')) {
			$output->writeln('<info>Running permission tests...</info>');
			$exitStatus |= $this->runTest('phpunit --testsuite integration-permission');
		}

		$filters = $input->getOption('filter');
		if (count($filters) > 0) {
			$output->writeln('<info>Running filtered tests...</info>');

			foreach ($input->getOption('filter') as $filter) {
				$output->writeln(sprintf('<info>Running %s tests...</info>', $filter));
				$exitStatus |= $this->runTest(sprintf('phpunit --filter %s', $filter));
			}
		}

		$testsuites = $input->getOption('testsuite');
		if (count($testsuites) > 0) {
			$output->writeln('<info>Running testsuites...</info>');

			foreach ($input->getOption('testsuite') as $testsuite) {
				$output->writeln(sprintf('<info>Running %s testsuite...</info>', $testsuite));
				$exitStatus |= $this->runTest(sprintf('phpunit --testsuite %s', $testsuite));
			}
		}
	}
}
