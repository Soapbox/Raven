<?php namespace SoapBox\Raven\Utils;

use SoapBox\Raven\Storage\ProjectStorage;
use SoapBox\Raven\Utils\DispatcherCommand;

class ProjectCommand extends DispatcherCommand
{
    private $path;
    private $namespace;

    public function __construct()
    {
        $projectStorage = ProjectStorage::getStorage();

        $this->command = $projectStorage->get('commands.name');
        $this->description = $projectStorage->get('commands.description');
        $this->path = $projectStorage->get('commands.path');
        $this->namespace = $projectStorage->get('commands.namespace');

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
