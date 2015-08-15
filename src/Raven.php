<?php namespace SoapBox\Raven;

use KevinGH\Version\Version;
use SoapBox\Raven\Commands;
use SoapBox\Raven\Utils\RavenStorage;
use SoapBox\Raven\Utils\SelfUpdater;
use SoapBox\Raven\Utils\DispatcherCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Raven extends Application {
	// private $selfUpdateCommand;
	private $storage;
	private $commands = [];

	public function __construct($name = 'Raven', $version = '@version@')
	{
		parent::__construct($name, $version);
		$this->registerCommands();

		$this->storage = RavenStorage::getStorage();
	}

	private function registerCommands()
	{
		$dir = __DIR__ . '/Commands';
		$files = scandir($dir);

		foreach ($files as $file) {
			if (is_file($dir . '/' . $file)) {
				$class = sprintf('SoapBox\Raven\Commands\%s', rtrim($file, '.php'));
				$this->add(new $class);
			}
		}
	}

	/**
	 * Check to see if the current version of Raven is out of date
	 */
	private function isOutdated()
	{
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
	private function initializeStyles(OutputInterface $output)
	{
		$output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));
	}

	public function run(InputInterface $input = null, OutputInterface $output = null)
	{
		if (null === $input) {
            $input = new ArgvInput();
        }

		return parent::run($input, $output);
	}

	public function doRun(InputInterface $input, OutputInterface $output)
	{
		$this->initializeStyles($output);

        $command = $this->getCommandName($input);

		if ($this->isOutdated() && $command != 'self-update') {
			$output->writeln(sprintf("<warning>There is a newer version of %s available. Run %s to update.</warning>\n",
				$this->getName(),
				'self-update'
			));
		}

		return parent::doRun($input, $output);
	}

	public function add(Command $command) 
	{
		$this->commands[$command->getName()] = $command;

		return parent::add($command);
	}

	public function get($name) 
	{
		if (isset($this->commands[$name])) {
			$command = $this->commands[$name];

			if ($command instanceof DispatcherCommand) {
				return $command;
			}
		}

		return parent::get($name);
	}
}
