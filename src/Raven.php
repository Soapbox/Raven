<?php namespace SoapBox\Raven;

use KevinGH\Version\Version;
use SoapBox\Raven\Commands;
use SoapBox\Raven\Utils\RavenStorage;
use SoapBox\Raven\Utils\SelfUpdater;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Raven extends Application {
	private $selfUpdateCommand;
	private $storage;

	public function __construct($name = 'Raven', $version = '@version@')
	{
		parent::__construct($name, $version);
		$this->registerCommands();

		$this->storage = RavenStorage::getStorage();
	}

	private function registerCommands()
	{
		$this->selfUpdateCommand = new Commands\SelfUpdateCommand;
		$this->add($this->selfUpdateCommand);

		$this->add(new Commands\ClearCacheCommand);
		$this->add(new Commands\DestroyCommand);
		$this->add(new Commands\EditCommand);
		$this->add(new Commands\GitConfigureCommand);
		$this->add(new Commands\HaltCommand);
		$this->add(new Commands\InitCommand);
		$this->add(new Commands\MakeCommand);
		$this->add(new Commands\ProvisionCommand);
		$this->add(new Commands\RebuildCommand);
		$this->add(new Commands\RefreshCommand);
		$this->add(new Commands\ResumeCommand);
		$this->add(new Commands\RunCommand);
		$this->add(new Commands\SshCommand);
		$this->add(new Commands\StatusCommand);
		$this->add(new Commands\SuspendCommand);
		$this->add(new Commands\WatchLogCommand);
		$this->add(new Commands\UpCommand);
		$this->add(new Commands\UpdateCommand);
		$this->add(new Commands\WorkbenchCommand);
		// $this->add(new Commands\TestCommand);
	}

	/**
	 * Check to see if the current version of Raven is out of date
	 */
	private function isOutdated() {
		$currentVersion = new Version($this->getVersion());
		$latestVersion = new Version($this->storage->get('latest_version'));
		if ($latestVersion->isGreaterThan($currentVersion)) {
			return true;
		}

		$lastUpdateCheck = (int) $this->storage->get('last_update_check');

		if (time() - $lastUpdateCheck < 60 * 60 * 8 && $lastUpdateCheck > 0) {
			return false;
		}

		$this->storage->set('last_update_check', time());

		$currentDir = getcwd();
		chRootDir();

		$selfUpdater = new SelfUpdater($this);
		$selfUpdater->getVersion()->setPreRelease(null);
		$update = $selfUpdater->getUpdate();

		chdir($currentDir);

		if ( !is_null($update) && $update->isNewer($selfUpdater->getVersion()) ) {
			$this->storage->set('latest_version', (string) $update->getVersion());
			return true;
		}

		return false;
	}

	/**
	 * Initialize custom styles
	 */
	private function initializeStyles(OutputInterface $output) {
		$output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));
	}

	public function run(InputInterface $input = null, OutputInterface $output = null)
	{
		if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $output = new ConsoleOutput();
        }

        $this->configureIO($input, $output);
        $this->initializeStyles($output);

        $command = $this->getCommandName($input);

		if ($this->isOutdated() && $command != $this->selfUpdateCommand->getName()) {
			$output->writeln(sprintf("<warning>There is a newer version of %s available. Run %s to update.</warning>\n",
				$this->getName(),
				$this->selfUpdateCommand->getName()
			));
		}

		return parent::run($input, $output);
	}
}
