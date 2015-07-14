<?php namespace SoapBox\SoapboxVagrant\Commands;

use RuntimeException;
use SoapBox\SoapboxVagrant\Utils\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ClearCacheCommand extends Command {
	protected $command = 'clear-cache';
	protected $description = 'Wipe the cache for your Laravel project.';

	private function wipeDirectory($path) {
		$files = glob($path . '/*');

		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->wipeDirectory($file);
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		if ( !is_dir('app/storage/cache') ) {
			throw new RuntimeException('Could not find any cache to remove.');
		}

		$this->wipeDirectory('app/storage/cache');
	}
}