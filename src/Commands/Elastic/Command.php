<?php

namespace SoapBox\Raven\Commands\Elastic;

use SoapBox\Raven\Commands\RunCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends RunCommand
{
    protected function addArguments()
    {
    }

    private function isElasticSearchRunning()
    {
        $result = 0;
        $this->runCommand('pgrep -f elasticsearch', $result);
        return $result === 0;
    }

    protected function ensureElasticSearchIsRunning(OutputInterface $output)
    {
        if (!$this->isElasticSearchRunning()) {
            $output->writeln('<info>Elasticsearch is not running! `raven elastic up`</info>');
            exit(1);
        }
    }

    protected function ensureElasticSearchIsNotRunning(OutputInterface $output)
    {
        if ($this->isElasticSearchRunning()) {
            $output->writeln('<info>Elasticsearch is running! You can stop it with halt, or you can run migrate, refresh, or halt.</info>');
            exit(1);
        }
    }

    private function isElasticSearchInstalled()
    {
        $result = 0;
        $this->runCommand('cd elasticsearch*', $result);
        return $result === 0;
    }

    protected function ensureElasticSearchIsInstalled(OutputInterface $output)
    {
        if (!$this->isElasticSearchInstalled()) {
            $output->writeln('<info>Elasticsearch is not installed! `raven elastic install`</info>');
            exit(1);
        }
    }

    protected function ensureElasticSearchIsNotInstalled(OutputInterface $output)
    {
        if ($this->isElasticSearchInstalled()) {
            $output->writeln('<info>Elasticsearch is already installed!</info>');
            exit(1);
        }
    }
}
