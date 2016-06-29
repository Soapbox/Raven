<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    protected $command = 'uninstall';
    protected $description = 'Uninstall the elasticsearch server.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureElasticSearchIsInstalled($output);
        $this->ensureElasticSearchIsNotRunning($output);

        $output->writeln('<info>Uninstalling elasticsearch...</info>');
        $this->runCommand('rm -rf /home/vagrant/elasticsearch-2.2.0');
        $output->writeln('<info>Done!</info>');
    }
}
