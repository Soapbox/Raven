<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchLogCommand extends Command {
	protected $command = 'watch-log';
	protected $description = 'Tail the latest log file.';
	private $vagrant;

	protected function addArguments() {
    $this->makeArgument('path')
      ->setDescription('The path to the log file to watch')
      ->setDefault('app/storage/logs');
  }

	protected function addOptions() {}

	public function execute(InputInterface $input, OutputInterface $output) {
    system('cd ' . $input->getArgument('path') . ' && tail -f `ls -t | head -1`');
	}
}
