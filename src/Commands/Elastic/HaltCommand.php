<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HaltCommand extends RunCommand
{
    protected $command = 'halt';
    protected $description = 'Halt the elasticsearch server.';

    protected function addArguments() 
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');
        $isRunning   = !$this->runMyCommand('pgrep -f elasticsearch');

        if (!$isInstalled) {
            $output->writeln('<info>Elasticsearch is not installed! `raven elastic install`</info>');
        }

        if ($isRunning) {
            $output->writeln('<info>Halting elasticsearch server...</info>');
            $this->runMyCommand('pgrep -f elasticsearch | xargs kill -9');
            $output->writeln('<info>Done!</info>');
        } else {
            $output->writeln('<info>Elasticsearch is not running! `raven elastic up`</info>');
        }
    }

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }
}
