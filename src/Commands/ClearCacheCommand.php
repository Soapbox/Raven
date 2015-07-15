<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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