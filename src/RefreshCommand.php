<?php namespace SoapBox\SoapboxVagrant;

use SoapBox\SoapboxVagrant\Utils\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class RefreshCommand extends Command
{
	protected $command = 'refresh';
	protected $description = 'Refresh and reseed the database';

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$process = new Process("php artisan migrate:rollback", getcwd(), array_merge($_SERVER, $_ENV));
		$process->run(function ($type, $line) use ($output) {
				$output->write($line);
		});

		$process = new Process("php artisan migrate --seed", getcwd(), array_merge($_SERVER, $_ENV));
		$process->run(function ($type, $line) use ($output) {
				$output->write($line);
		});
	}
}
