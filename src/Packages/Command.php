<?php

namespace SoapBox\Raven\Packages;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SoapBox\Raven\Api\Packages\Command as CommandContract;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    public function __construct(string $namespace, CommandContract $command)
    {
        $this->namespace = $namespace;
        $this->command = $command;

        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $config = new Configuration();
        $this->command->configure($config);

        $name = $config->getName();

        if (0 !== stripos($name, $this->namespace . ':')) {
            $name = sprintf('%s:%s', $this->namespace, $name);
        }

        $this->setName($name);
        $this->setDescription($config->getDescription());
        $this->setHelp($config->getHelp());

        foreach ($config->getArguments() as $argument) {
            $this->addArgument(
                $argument->getName(),
                $argument->getMode(),
                $argument->getDescription(),
                $argument->getDefault()
            );
        }

        foreach ($config->getOptions() as $option) {
            $this->addOption(
                $option->getName(),
                $option->getShortcut(),
                $option->getMode(),
                $option->getDescription(),
                $option->getDefault()
            );
        }
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     *         When this abstract method is not implemented
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *        An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *        An OutputInterface instance
     *
     * @return null|int
     *         null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->command->execute(new Input($input), new Output($output));
    }

    /**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *        An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *        An OutputInterface instance
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->command->interact(new Input($input), new Output($output));
    }

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *        An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *        An OutputInterface instance
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->command->initialize(new Input($input), new Output($output));
    }
}
