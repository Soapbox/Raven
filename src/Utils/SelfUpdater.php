<?php namespace SoapBox\Raven\Utils;

use Herrera\Phar\Update\Manifest;
use KevinGH\Version\Version;
use Symfony\Component\Console\Application;
use Symfony\Component\Process\Process;

class SelfUpdater
{
    const MANIFEST = 'manifest.json';

    private $application;
    private $manifest;
    private $update;
    private $version;

    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->version = new Version($this->application->getVersion());
    }

    private function getManifest()
    {
        if (is_null($this->manifest)) {
            $this->manifest = Manifest::loadFile(self::MANIFEST);
        }

        return $this->manifest;
    }

    private function run($command, $callback = null)
    {
        $process = new Process($command);
        $process->run($callback);
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getUpdate($major = false, $pre = false)
    {
        if (is_null($this->update)) {
            $this->run('git fetch --all');
            $this->run('git checkout origin/releases -- manifest.json');

            if (!$pre && !empty($this->version->getPreRelease())) {
                if ($major) {
                    $this->version->setMajor(0);
                }

                $this->version->setMinor(0);
                $this->version->setPatch(0);
                $this->version->setPreRelease(0);
            }

            $manifest = $this->getManifest();
            $this->update = $manifest->findRecent($this->getVersion(), $major, $pre);

            $this->run('git reset HEAD manifest.json');
            $this->run('rm manifest.json');
        }

        return $this->update;
    }

    public function update($major = false, $pre = false)
    {
        $update = $this->getUpdate();

        if (!is_null($update) && $update->isNewer($this->getVersion())) {
            $newVersion = $update->getVersion();
            $file = $update->getUrl();

            $this->run("git checkout origin/releases -- releases/$file");
            $this->run("git reset HEAD releases/$file");
            $this->run("mv releases/$file $file; rm -r releases");

            $update->getFile();

            $this->run("rm $file");

            $update->copyTo(realpath($_SERVER['argv'][0]));

            return true;
        }

        return false;
    }
}
