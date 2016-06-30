<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends Command
{
    protected $command = 'up';
    protected $description = 'Boot the elasticsearch server.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->ensureElasticSearchIsInstalled($output);
        $this->ensureElasticSearchIsNotRunning($output);

        date_default_timezone_set('UTC');
        $logPath = sprintf('/home/deploy/client/elasticsearch/%s', date('Y-m-d'));

        $output->writeln('<info>Booting up elasticsearch...</info>');
        $this->runCommand('nohup ~/elasticsearch-*/bin/elasticsearch & sleep 1');

        $output->writeln(sprintf('<info>Creating log directories in %s ...</info>', $logPath));
        $this->runCommand(sprintf('sudo mkdir -m u=rwx -p %s', $logPath));

        $output->writeln('<info>Done!</info>');
    }
}
