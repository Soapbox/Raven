<?php namespace SoapBox\Raven\Commands\Util;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FunCommand extends Command {
	protected $command = 'fun';
	protected $description = 'Run a utility.';

	protected function addArguments() {
		$this->makeArgument('blah')
			->setDescription('halb')
			->required();
		$this->makeArgument('test')
			->setDescription('halb')
			->isArray()
			->required();
	}

	protected function addOptions() {}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		var_dump($input->getArgument('blah'));
		var_dump($input->getArgument('test'));
	}
}