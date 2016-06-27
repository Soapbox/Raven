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
        $this->makeArgument('argument')
            ->setDescription('[install|up|migrate|refersh|halt]')
            ->required();

        $this->makeArgument('install')
            ->setDescription('Install elasticsearch in vagrant.')
            ->optional();

        $this->makeArgument('up')
            ->setDescription('Boot the elasticsearch server.')
            ->optional();

        $this->makeArgument('migrate')
            ->setDescription('Reindex daily documents.')
            ->optional();

        $this->makeArgument('refresh')
            ->setDescription('Delete index and reindex all documents.')
            ->optional();

        $this->makeArgument('halt')
            ->setDescription('Halt the elasticsearch server.')
            ->optional();
    }

    protected function addOptions() {}

    private function runMyCommand($command)
    {
        $return = 0;
        $this->runCommand($command, $return);
        return $return;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cdToHome =  'cd Development/soapbox/soapbox-v4/ && ';
        $isInstalled = !$this->runMyCommand('cd elasticsearch*');
        $arg = $input->getArgument('argument');

        if ($isInstalled) {
            if ($arg === 'install') {
                $output->writeln('<info>Elastic search is already installed!</info>');
            }

            if ($arg === 'up') {
                $output->writeln('<info>Booting up elasticsearch...</info>');
                $this->runMyCommand('nohup ~/elasticsearch-*/bin/elasticsearch & sleep 1');
                $output->writeln('<info>Done!</info>');
            }

            if ($arg === 'migrate') {
                $output->writeln('<info>Indexing documents into elasticsearch...</info>');
                $this->runMyCommand($cdToHome.'php artisan elasticsearch:daily --reindex=true');
                $output->writeln('<info>Done!</info>');
            }

            if ($arg === 'refresh') {
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
            }

            if ($arg === 'halt') {
                $output->writeln('<info>Halting elasticsearch server...</info>');
                $this->runMyCommand('pgrep -f elasticsearch | xargs kill -9');
                $output->writeln('<info>Done!</info>');
            }
        } else {
            if ($arg === 'install') {
                $output->writeln('<info>Installing elasticsearch...</info>');
                $output->writeln('<info>Done!</info>');
            } else {
                $output->writeln('<info>Please install elasticsearch first. `raven elasticsearch --install`</info>');
            }
        }

    }
}
