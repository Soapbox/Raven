<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected $command = 'install';
    protected $description = 'Install the elasticsearch server.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureElasticSearchIsNotInstalled($output);

        $output->writeln('<info>Installing elasticsearch...</info>');
        $this->runCommand('/vagrant/post-installation/elastic-search');
        $output->writeln('<info>Done!</info>');
    }
}
