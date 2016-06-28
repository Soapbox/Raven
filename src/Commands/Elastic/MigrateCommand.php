<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends RunCommand
{
    protected $command = 'migrate';
    protected $description = 'Reindex daily documents.';

    protected function addArguments() 
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');
        $isRunning   = !$this->runMyCommand('pgrep -f elasticsearch');
        $cdToHome    = 'cd Development/soapbox/soapbox-v4/ && ';

        if (!$isInstalled) {
            $output->writeln('<info>Elasticsearch is not installed! `raven elastic install`</info>');
        }

        if ($isRunning) {
            $output->writeln('<info>Indexing documents into elasticsearch...</info>');
            $this->runMyCommand($cdToHome.'php artisan elasticsearch:daily --reindex=true');
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
