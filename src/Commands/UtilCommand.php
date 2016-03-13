<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use SoapBox\Raven\Utils\DispatcherCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use SoapBox\Raven\Commands\Utility\FunCommand;
use Symfony\Component\Console\Input\ArgvInput;

class UtilCommand extends DispatcherCommand
{
    protected $command = 'util';
    protected $description = 'Run a utility.';
}
