<?php namespace SoapBox\Raven\Storage;

use RuntimeException;
use SoapBox\Raven\Helpers\GitHelper;

class ProjectStorage extends ReadableStorage
{
    protected static $instance;

    private $projectRoot = '';

    protected function __construct()
    {
        $root = getcwd();
        $file = $root . '/raven.json';
        if (!file_exists($file)) {
            try {
                $root = GitHelper::getRepositoryRoot();

                $file = $root  . '/raven.json';
            } catch (RuntimeException $e) {

            }
        }

        $this->loadFile($file);

        if ($this->exists()) {
            $this->projectRoot = $root;
        }
    }

    /**
     * Get the rot directory of the current project
     *
     * @return string
     */
    public function getProjectRoot()
    {
        return $this->projectRoot;
    }

    public function getCommandDir()
    {
        return sprintf('%s/%s', $this->getProjectRoot(), $this->get('commands.path'));
    }

    /**
     * Determine whether or not there are any project commands
     *
     * @return boolean
     */
    public function hasCommands()
    {
        return $this->has('commands') &&
            !empty(array_diff(scandir($this->getCommandDir()), ['.', '..']));
    }
}
