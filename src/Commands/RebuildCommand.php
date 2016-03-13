<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RebuildCommand extends RunCommand
{
    protected $command = 'rebuild';
    protected $description = 'Rebuild the database';

    protected function addArguments()
    {

    }
    protected function addOptions()
    {

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $currentDir = getcwd();

        chRootDir();

        $output->writeln('<info>Rebuilding the database...</info>');

        $command = '
            mysql -u soapbox -psecret -e \'DROP DATABASE \\`soapbox-v4\\`\'
            mysql -u soapbox -psecret -e \'CREATE DATABASE \\`soapbox-v4\\`\'
        ';

        $returnStatus = 0;

        $this->runCommand($command, $returnStatus, false);

        if ($returnStatus !== 0) {
            throw new RuntimeException('Failed to rebuild the database.');
        }

        $output->writeln('<info>Rebuild complete.</info>');
        $output->writeln('');

        chdir($currentDir);

        $process = new Process("php artisan migrate --seed --ansi");
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to run database migrations.');
        }
    }
}
