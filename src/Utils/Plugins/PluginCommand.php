<?php

namespace SoapBox\Raven\Utils\Plugins;

use SoapBox\Raven\Utils\Command;
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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pluginCommand->run(
            new Input($input),
            new Output($output)
        );
    }
}
