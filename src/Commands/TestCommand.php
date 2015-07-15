<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command {
	protected $command = 'test';
	protected $description = 'Run the testsuites.';

	protected function addOptions() {
		$this->makeOption('unit')
			->addShortcut('u')
			->setDescription('Run the unit testsuite.');


		$this->makeOption('integration')
			->addShortcut('i')
			->setDescription('Run the integration testsuite.');


		$this->makeOption('permission')
			->addShortcut('p')
			->setDescription('Run the permission testsuite.');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
	}
}