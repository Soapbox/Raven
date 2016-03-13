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
}
