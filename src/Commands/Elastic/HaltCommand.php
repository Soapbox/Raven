<?php

namespace SoapBox\Raven\Commands\Elastic;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HaltCommand extends Command
{
    protected $command = 'halt';
    protected $description = 'Halt the elasticsearch server.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureElasticSearchIsInstalled($output);
        $this->ensureElasticSearchIsRunning($output);

        $output->writeln('<info>Halting elasticsearch server...</info>');
        $this->runCommand('pgrep -f elasticsearch | xargs kill -9');
        $output->writeln('<info>Done!</info>');
    }
}
