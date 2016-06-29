<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{
    protected $command = 'refresh';
    protected $description = 'Delete all indexes and reindex all documents.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureElasticSearchIsInstalled($output);
        $this->ensureElasticSearchIsRunning($output);

        $artisanPath  = '~/Development/soapbox/soapbox-v4';

        $command = sprintf('
            php %s/artisan index:audits --add=true &&
            php %s/artisan elasticsearch:daily --reindex=true &&
            php %s/artisan elasticsearch:audits --mapping=true &&
            php %s/artisan elasticsearch:audits --reindex=true &&
            php %s/artisan index:audits --drop=true
        ', $artisanPath, $artisanPath, $artisanPath, $artisanPath, $artisanPath);

        $output->writeln('<info>Deleting elasticsearch indexes...</info>');
        $this->runCommand('curl -XDELETE localhost:9200/*');

        $output->writeln('<info>Reindexing elasticsearch indexes...</info>');
        $this->runCommand($command);
        $output->writeln('<info>Done!</info>');
    }
}
