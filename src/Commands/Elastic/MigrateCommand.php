<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected $command = 'migrate';
    protected $description = 'Reindex daily documents.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->ensureElasticSearchIsInstalled($output);
        $this->ensureElasticSearchIsRunning($output);

        $artisanPath  = '~/Development/soapbox/soapbox-v4';
        $command = sprintf('php %s/artisan elasticsearch:daily --reindex=true', $artisanPath);

        $output->writeln('<info>Indexing documents into elasticsearch...</info>');
        $this->runCommand($command);
        $output->writeln('<info>Done!</info>');
    }
}
