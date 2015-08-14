<?php namespace SoapBox\Raven\Utils;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class DispatcherCommand extends Command {
	private $commands = [];
	private $commandString;
	private $argv;

	public function __construct($name = null)
    {
    	parent::__construct($name);

    	$this->registerCommands();
        $this->generateCommandList();
    }

	private function generateCommandList() {
		$section = $this->makeSection('Available commands');

		$maxLength = 0;
		foreach($this->commands as $command) {
			if (strlen($command->getName()) > $maxLength) {
				$maxLength = strlen($command->getName());
			}
		}
		$maxLength += 2;

		foreach($this->commands as $command) {
			$section->addContent(sprintf(
				'<info>%s</info>%s%s',
				$command->getName(),
				str_repeat(' ', $maxLength - strlen($command->getName())),
				$command->getDescription()
			));
		}
	}

	/**
	 * Set up this object based on the command's input
	 *
	 * @var Symfony\Component\Console\Input\InputInterface $input The command's input
	 */
	private function setUp(InputInterface $input) {
		$argv = $input->getArgument('args');
		if (count($argv) > 0) {
			$this->commandString = $argv[0];
			$this->argv = array_merge([$this->getName()], $argv);
		}
	}

	/**
	 * Get the command to run base on the input
	 *
	 * @var Symfony\Component\Console\Input\InputInterface $input The command's input
	 * @return Symfony\Component\Console\Command\Command
	 * @throws RuntimeException
	 */
	private function getCommand(InputInterface $input) {
		if ($input->getOption('help') || is_null($this->commandString)) {
			$helpCommand = $this->getApplication()->get('help');

			if (!is_null($this->commandString)) {
				$command = $this->findCommand($this->commandString);
			} else {
				$class = get_class($this);
				$command = new $class;
				$command->setApplication($this->getApplication());
			}

			$helpCommand->setCommand($command);
			$this->argv = ['help', $command->getName()];

			return $helpCommand;
		}

		return $this->findCommand($this->commandString);
	}

	/**
	 * Find a command registered to this command by its name
	 *
	 * @var string $command The name of the command to find
	 * @return Symfony\Component\Console\Command\Command
	 * @throws RuntimeException
	 */
	private function findCommand($command) {
		if (array_key_exists($command, $this->commands)) {
			return $this->commands[$command];
		}

		throw new RuntimeException(sprintf('There is no command registered with the "%s" name.', $command));
	}

	/**
	 * This method specifies the arguments the command accepts
	 */
	protected function addArguments() {
		$this->makeArgument('args')
			->setDescription('The command to run and its arguments')
			->isArray();
	}

	/**
	 * This function is used to register commands with this dispatcher
	 */
	abstract protected function registerCommands();

	/**
	 * Register a command with this dispatcher
	 *
	 * @var Symfony\Component\Console\Command\Command $command The command to register
	 */
	protected function addCommand(SymfonyCommand $command) {
		$this->commands[$command->getName()] = $command;
		$command->setApplication($this->getApplication());
	}

	/**
	 * Execute the current command
	 *
	 * @var Symfony\Component\Console\Input\InputInterface $input The command's input
	 * @var Symfony\Component\Console\Output\OutputInterface $output The output for the command
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setUp($input);

		if (is_null($this->commandString)) {
			$this->getApplication()->get('help');
		}
		$command = $this->getCommand($input);
		$arrayInput = new ArgvInput($this->argv, $command->getDefinition());
		$command->run($arrayInput, $output);
	}
}