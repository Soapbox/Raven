<?php namespace SoapBox\Raven\Utils;

use SoapBox\Raven\Utils\DispatcherCommand;

class ProjectCommand extends DispatcherCommand
{
    private $path;
    private $namespace;

    public function __construct($command, $description, $path, $namespace)
    {
        $this->command = $command;
        $this->description = $description;
        $this->path = $path;
        $this->namespace = $namespace;

        parent::__construct();
    }

    /**
     * Get the directory that contains all of the commands for this dispatcher
     *
     * @return string
     */
    protected function getCommandDirectory()
    {
        return $this->path;
    }

    /**
     * Get the namespace for the commands
     *
     * @return string
     */
    protected function getCommandNamespace()
    {
        return $this->namespace;
    }
}
