<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends RunCommand
{
    protected $command = 'up';
    protected $description = 'Boot the elasticsearch server.';

    protected function addArguments() 
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        date_default_timezone_set('UTC');
        $isRunning = !$this->runMyCommand('pgrep -f elasticsearch');
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');
        $cdToHome    = 'cd Development/soapbox/soapbox-v4/ && ';
        $logDirPath  = '/home/deploy/client/elasticsearch/'.date('Y-m-d');

        if (!$isRunning) {
            $output->writeln('<info>Booting up elasticsearch...</info>');
            $this->runMyCommand('nohup ~/elasticsearch-*/bin/elasticsearch & sleep 1');
            $output->writeln('<info>Creating log directories in '.$logDirPath.' ...</info>');
            $this->runMyCommand('sudo mkdir -m u=rwx -p '.$logDirPath);
            $output->writeln('<info>Done!</info>');
        } else {
            $output->writeln('<info>Elasticsearch is already running! You can now migrate, refresh, or halt.</info>');
        }
    }

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }
}
