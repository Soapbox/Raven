<?php namespace SoapBox\Raven\Utils;

use ReflectionClass;
use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class DispatcherCommand extends Command
{
    private $commands = [];
    private $commandString;
    private $argv;
    private $commandsRegistered = false;

    public function getSynopsis($bool = false)
    {
        return sprintf('%s command [options] [arguments]', $this->getName());
    }

    /**
     * This method generated the available command list for the help output.
     */
    public function generateCommandList()
    {
        $this->registerCommands();
        $section = $this->makeSection('Available commands');

        $maxLength = 0;
        foreach ($this->commands as $command) {
            if (strlen($command->getName()) > $maxLength) {
                $maxLength = strlen($command->getName());
            }
        }
        $maxLength += 2;

        foreach ($this->commands as $command) {
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
     * @param Symfony\Component\Console\Input\InputInterface $input The command's input
     */
    private function setUp(InputInterface $input)
    {
        $this->registerCommands();

        $this->argv = $_SERVER['argv'];
        array_shift($this->argv);
        if (count($this->argv) > 1) {
            $this->commandString = $this->argv[1];
        }
    }

    /**
     * Get the command to run base on the input
     *
     * @param Symfony\Component\Console\Input\InputInterface $input The command's input
     * @return Symfony\Component\Console\Command\Command
     * @throws RuntimeException
     */
    private function getCommand(InputInterface $input)
    {
        if ($input->getOption('help') || is_null($this->commandString)) {
            $helpCommand = $this->getApplication()->get('help');

            if (!is_null($this->commandString)) {
                $command = $this->findCommand($this->commandString);
            } else {
                $class = get_class($this);
                $command = new $class;
                $command->setApplication($this->getApplication());
                $command->setDefinition(new InputDefinition());
                $command->generateCommandList();
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
     * @param string $command The name of the command to find
     * @return Symfony\Component\Console\Command\Command
     * @throws RuntimeException
     */
    private function findCommand($command)
    {
        if (array_key_exists($command, $this->commands)) {
            return $this->commands[$command];
        }

        throw new RuntimeException(sprintf('There is no command registered with the "%s" name.', $command));
    }

    /**
     * This method specifies the arguments the command accepts
     */
    protected function addArguments()
    {
        $this->makeArgument('args')
            ->setDescription('The command to run and its arguments')
            ->isArray();
    }

    /**
     * Get the directory that contains all of the commands for this dispatcher
     *
     * @return string
     */
    protected function getCommandDirectory()
    {
        $classInfo = new ReflectionClass($this);
        return dirname($classInfo->getFileName()) . '/' . ucfirst($this->getName());
    }

    /**
     * Get the namespace for the commands
     *
     * @return string
     */
    protected function getCommandNamespace()
    {
        $classInfo = new ReflectionClass($this);
        return sprintf('%s\%s', $classInfo->getNamespaceName(), ucfirst($this->getName()));
    }

    /**
     * This function is used to register commands with this dispatcher
     */
    private function registerCommands()
    {
        if ($this->commandsRegistered) {
            return;
        }

        $dir = $this->getCommandDirectory();
        $namespace = $this->getCommandNamespace();
        $files = scandir($dir);

        foreach ($files as $file) {
            $class = sprintf('%s\%s', $namespace, trim($file, '.php'));
            $this->addCommand(new $class);
        }

        $this->commandsRegistered = true;
    }

    /**
     * Register a command with this dispatcher
     *
     * @param Symfony\Component\Console\Command\Command $command The command to register
     */
    private function addCommand(SymfonyCommand $command)
    {
        $this->commands[$command->getName()] = $command;
        $command->setApplication($this->getApplication());
    }

    /**
     * This function is called before the command is dispatched. This allows you to hook into the dispatcher
     * before the command is run. If this function returns a non-zero number, the command will not run and
     * the exit status will be the number returned from this function.
     *
     * @param Symfony\Component\Console\Command\Command $command The command to be executed
     * @param Symfony\Component\Console\Input\InputInterface $input The command's input
     * @param Symfony\Component\Console\Output\OutputInterface $output The output for the command
     * @return int 0 or an exit status
     */
    protected function beforeDispatch(SymfonyCommand $command, InputInterface $input, OutputInterface $output)
    {

    }

    /**
     * This function is called after the command has executed. This allows you to hook into the dispatcher
     * after the command get dispatched. The input into this function is the exit status from the dispatched
     * command.
     *
     * @param int $exitStatus The exit status of the dispatched command
     * @param Symfony\Component\Console\Input\InputInterface $input The command's input
     * @param Symfony\Component\Console\Output\OutputInterface $output The output for the command
     */
    protected function afterDispatch($exitStatus, InputInterface $input, OutputInterface $output)
    {

    }

    /**
     * Execute the current command
     *
     * @param Symfony\Component\Console\Input\InputInterface $input The command's input
     * @param Symfony\Component\Console\Output\OutputInterface $output The output for the command
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setUp($input);

        $command = $this->getCommand($input);

        if ($exitStatus = $this->beforeDispatch($command, $input, $output)) {
            return $exitStatus;
        }

        $command->mergeApplicationDefinition();
        $arrayInput = new ArgvInput($this->argv, $command->getDefinition());
        $exitStatus = $command->run($arrayInput, $output);

        $this->afterDispatch($exitStatus, $input, $output);

        return $exitStatus;
    }
}
