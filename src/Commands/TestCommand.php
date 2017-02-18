<?php

namespace SoapBox\Raven\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Creates new users.')
            ->setHelp("This command allows you to create users...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln((string) $input);
    }
}
