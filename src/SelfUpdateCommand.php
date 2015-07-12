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
		$selfUpdater = new SelfUpdater($this->getApplication());
		$update = $selfUpdater->getUpdate();
		if ($update->isNewer($this->getApplication()->getVersion())) {
			$output->writeln('New version of <info>raven</raven> available.');
			$output->writeln(sprintf(
				'Updating from <comment>%s</comment> to <comment>%s</comment>',
				$this->getApplication()->getVersion(),
				$update->getVersnio()
			));

			$selfUpdater->update();

			$output->writeln(sprintf(
				'<info>raven</info> was successfully updated to version <comment>%s</comment>',
				$update->getVersion()
			));
		}
	}
}
