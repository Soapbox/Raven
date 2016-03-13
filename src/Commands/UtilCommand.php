<?php namespace SoapBox\Raven\Commands;

use SoapBox\Raven\Utils\DispatcherCommand;

class UtilCommand extends DispatcherCommand
{
    protected $command = 'util';
    protected $description = 'Run a utility.';
}
