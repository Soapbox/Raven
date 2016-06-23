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
            ->addShortcut('u')
            ->setDescription('Boot the elasticsearch server.')
            ->boolean();

        $this->makeOption('migrate')
            ->addShortcut('m')
            ->setDescription('Reindex daily documents.')
            ->boolean();

        $this->makeOption('refresh')
            ->addShortcut('r')
            ->setDescription('Delete index and reindex all documents.')
            ->boolean();

        $this->makeOption('halt')
            ->addShortcut('h')
            ->setDescription('Halt the elasticsearch server.')
            ->boolean();
    }

    private function runMyCommand($command)
    {
        $this->runCommand($command, 0);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('up')) {
            $output->writeln('<info>Booting up elasticsearch...</info>');
            $this->runMyCommand('~/elasticsearch-*/bin/elasticsearch');
        }

        if ($input->getOption('migrate')) {
            $output->writeln('<info>Indexing documents into elasticsearch...</info>');
            $this->runMyCommand('php artisan elasticsearch:daily --reindex=true');
        }

        if ($input->getOption('refresh')) {
            $output->writeln('<info>Deleting elasticsearch indexes...</info>');
            $this->runMyCommand('curl -XDELETE localhost:9200/*');
            $output->writeln('<info>Reindexing elasticsearch indexes...</info>');
            $this->runMyCommand('php artisan elasticsearch:daily --reindex=true');
            $this->runMyCommand('php artisan elasticsearch:audits --mapping=true');
            $this->runMyCommand('php artisan elasticsearch:audits --reindex=true');
        }

        if ($input->getOption('halt')) {
            $output->writeln('<info>Halting elasticsearch server...</info>');
            $this->runMyCommand("ps axf | grep elasticsearch | grep -v grep | awk '{print \"kill -9 \" $1} | sh");
        }

        $output->writeln('<info>Done!</info>');
    }
}
