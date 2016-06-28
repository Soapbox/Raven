<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends RunCommand
{
    protected $command = 'install';
    protected $description = 'Install the elasticsearch server.';

    protected function addArguments() 
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');

        if ($isInstalled) {
            $output->writeln('<info>Elasticsearch is already installed!</info>');
        } else {
            $output->writeln('<info>Installing elasticsearch...</info>');
            $this->runMyCommand('/vagrant/post-installation/elastic-search');
            $output->writeln('<info>Done!</info>');
        }
    }

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }
}
