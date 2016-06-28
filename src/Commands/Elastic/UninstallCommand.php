<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends RunCommand
{
    protected $command = 'uninstall';
    protected $description = 'Uninstall the elasticsearch server.';

    protected function addArguments() 
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');

        if ($isInstalled) {
            $output->writeln('<info>Uninstalling elasticsearch...</info>');
            $this->runMyCommand('rm -rf /home/vagrant/elasticsearch-2.2.0');
            $output->writeln('<info>Done!</info>');
        } else {
            $output->writeln('<info>Elasticsearch is not installed!</info>');
        }
    }

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }
}
