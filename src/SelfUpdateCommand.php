<?php namespace SoapBox\SoapboxVagrant;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
		$manager = new Manager(Manifest::loadFile(self::MANIFEST));
		$manager->update($this->getApplication()->getVersion(), true);
	}
}
