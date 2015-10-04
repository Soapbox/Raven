<?php namespace SoapBox\Raven;

use Exception;
use KevinGH\Version\Version;
use SoapBox\Raven\Commands;
use SoapBox\Raven\Utils\ArgvInput;
use SoapBox\Raven\Utils\DispatcherCommand;
use SoapBox\Raven\Utils\RavenStorage;
use SoapBox\Raven\Utils\SelfUpdater;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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
				$c = $this->add(new $class);
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

		if (null === $output) {
			$output = new ConsoleOutput();
		}

		try {
			$commandName = $this->getCommandName($input);
			if (!empty($commandName)) {
				$command = $this->find($commandName);
				if ($command instanceof DispatcherCommand) {
					$input->makeDispatcher();
				}
			}
		} catch (Exception $e) {
			if ($output instanceof ConsoleOutputInterface) {
				$this->renderException($e, $output->getErrorOutput());
			} else {
				$this->renderException($e, $output);
			}

			$exitCode = $e->getCode();
			if (is_numeric($exitCode)) {
				$exitCode = (int) $exitCode;
				if (0 === $exitCode) {
					$exitCode = 1;
				}
			} else {
				$exitCode = 1;
			}

			exit($exitCode);
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

	public function find($name)
	{
		if (isset($this->commands[$name])) {
			$command = $this->commands[$name];

			if ($command instanceof DispatcherCommand) {
				return $command;
			}
		}

		return parent::find($name);
	}
}
