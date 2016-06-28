<?php namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand; 
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends RunCommand
{
    protected $command = 'refresh';
    protected $description = 'Delete all indexes and reindex all documents.';

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
            $output->writeln('<info>Deleting elasticsearch indexes...</info>');
            $this->runMyCommand('curl -XDELETE localhost:9200/*');
            $output->writeln('<info>Reindexing elasticsearch indexes...</info>');
            $this->runMyCommand($cdToHome.'
                php artisan index:audits --add=true &&
                php artisan elasticsearch:daily --reindex=true &&
                php artisan elasticsearch:audits --mapping=true &&
                php artisan elasticsearch:audits --reindex=true &&
                php artisan index:audits --drop=true
            ');
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
