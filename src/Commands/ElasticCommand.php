<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCommand extends RunCommand
{
    protected $command = 'elasticsearch';
    protected $description = 'Boot and index elasticsearch.';
    private $vagrant;

    public function isEnabled()
    {
        return true;
    }

    protected function addArguments()
    {

    }

    protected function addOptions()
    {
        $this->makeOption('up')
            ->setDescription('Boot the elasticsearch server.')
            ->boolean();

        $this->makeOption('migrate')
            ->setDescription('Reindex daily documents.')
            ->boolean();

        $this->makeOption('refresh')
            ->setDescription('Delete index and reindex all documents.')
            ->boolean();

        $this->makeOption('halt')
            ->setDescription('Halt the elasticsearch server.')
            ->boolean();
    }

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cdToHome =  'cd Development/soapbox/soapbox-v4/ && ';

        if ($input->getOption('up')) {
            $output->writeln('<info>Booting up elasticsearch...</info>');
            $this->runMyCommand('nohup ~/elasticsearch-*/bin/elasticsearch & sleep 1');
        }

        if ($input->getOption('migrate')) {
            $output->writeln('<info>Indexing documents into elasticsearch...</info>');
            $this->runMyCommand($cdToHome.'php artisan elasticsearch:daily --reindex=true');
        }

        if ($input->getOption('refresh')) {
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
        }

        if ($input->getOption('halt')) {
            $output->writeln('<info>Halting elasticsearch server...</info>');
            $this->runMyCommand('pgrep -f elasticsearch | xargs kill -9');
        }

        $output->writeln('<info>Done!</info>');
    }
}
