<?php namespace SoapBox\Raven\Commands\Utility;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrazyWhackNutsCommand extends Command {
	protected $command = 'crazy-whack-nuts';
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