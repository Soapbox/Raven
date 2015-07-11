<?php namespace SoapBox\SoapboxVagrant;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use SoapBox\SoapboxVagrant\Utils\SelfUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class SelfUpdateCommand extends Command
{
	const MANIFEST = 'https://soapbox.github.io/raven/manifest.json';

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('self-update')
			->setDescription('Updates raven to the latest version');
	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$process = new Process('git fetch --all', realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
		$process->run();

		$process = new Process('git checkout origin/releases -- manifest.json', realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
		$process->run();


		$selfUpdater = new SelfUpdater($this->getApplication());
		$update = $selfUpdater->getUpdate();

		if ( !is_null($update) ) {
			$file = $update->getUrl();

			$process = new Process("git checkout origin/releases -- releases/$file", realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
			$process->run();

			$process = new Process("mv releases/$file $file; rm -r releases", realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
			$process->run();

			$update->getFile();
			$update->copyTo(realpath($_SERVER['argv'][0]));

		}

		// var_dump($this->getApplication()->getVersion());
		// var_dump($selfUpdater->getLatestVersion());
		//
		// $process = new Process('rm manifest.json', realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);
		// $process->run();
		// die();
		// $manager = new Manager(Manifest::loadFile(self::MANIFEST));
		// $manager->update($this->getApplication()->getVersion(), true);
	}
}
