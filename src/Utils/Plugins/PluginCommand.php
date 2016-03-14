<?php

namespace SoapBox\Raven\Utils\Plugins;

use SoapBox\Raven\Utils\Command;
use SoapBox\Raven\Helpers\ArrayHelper;
use SoapBox\Raven\Storage\ProjectStorage;
use Raven\Api\Command\Command as RavenCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginCommand extends Command
{
    private $pluginCommand;

    public function __construct(RavenCommand $command)
    {
        $projectStorage = ProjectStorage::getStorage();

        $this->command = $command->getName();
        $this->description = $command->getDescription();
        $this->pluginCommand = $command;

        parent::__construct();
    }

    protected function addArguments()
    {
        $arguments = $this->pluginCommand->getArguments();

        foreach ($arguments as $argument) {
            $this->makeArgument($argument[0])
                ->addMode(ArrayHelper::get($argument, 1))
                ->setDescription(ArrayHelper::get($argument, 2, ''))
                ->setDefault(ArrayHelper::get($argument, 3));
        }
    }

    protected function addOptions()
    {
        $options = $this->pluginCommand->getOptions();

        foreach ($options as $option) {
            $this->makeArgument($option[0])
                ->addShortcut(ArrayHelper::get($option, 1))
                ->setMode(ArrayHelper::get($option, 2))
                ->setDescription(ArrayHelper::get($option, 3, ''))
                ->setDefault(ArrayHelper::get($option, 4));
        }
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pluginCommand->run(
            new Input($input),
            new Output($output)
        );
    }
}
