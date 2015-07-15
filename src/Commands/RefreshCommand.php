<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RefreshCommand extends Command
{
	protected $command = 'refresh';
	protected $description = 'Refresh and reseed the database';

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$process = new Process("php artisan migrate:refresh --seed --ansi");
		$process->run(function ($type, $line) use ($output) {
				$output->write($line);
		});
		
		if ( !$process->isSuccessful() ) {
			throw new RuntimeException('Failed to rollback and re-run the database migraions.');
		}
	}
}
