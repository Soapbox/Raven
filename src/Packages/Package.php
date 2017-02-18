<?php

namespace SoapBox\Raven\Packages;

use SplFileInfo;
use FilesystemIterator;
use CallbackFilterIterator;
use Composer\Autoload\ClassLoader;
use SoapBox\Raven\Api\Packages\Command;
use SoapBox\Raven\Utilities\Collection;

class Package
{
    private $packageFile;
    private $fileData;
    private $loader;

    public function __construct(SplFileInfo $packageFile, ClassLoader $loader)
    {
        $this->packageFile = $packageFile;
        $this->loader = $loader;

        $file = $packageFile->openFile();
        $this->fileData = new Collection(
            json_decode($file->fread($file->getSize()), true)
        );
    }

    private function get(string $key)
    {
        return $this->fileData->get($key);
    }

    public function getNamespace(): string
    {
        return $this->get('command_namespace');
    }


    private function getCommandsForDirectory(string $namespace, SplFileInfo $directory)
    {
        $commands = new Collection();

        $this->loader->addPsr4($namespace, $directory->getPathname());

        $commandFiles = new CallbackFilterIterator(
            new FilesystemIterator($directory),
            function ($current) {
                return $current->getExtension() === 'php';
            }
        );

        foreach ($commandFiles as $commandFile) {
            $class = sprintf(
                '%s\\%s',
                rtrim($namespace, '\\'),
                preg_replace('/.php$/', '', $commandFile->getFilename())
            );

            if (class_exists($class)) {
                $command = new $class;

                if ($command instanceof Command) {
                    $commands->push($command);
                }
            }
        }

        return $commands;
    }

    public function getCommands(): Collection
    {
        $commands = new Collection();

        foreach ($this->fileData->get('commands') as $namespace => $directory) {
            $commandDirectory = new SplFileInfo(sprintf(
                '%s/%s',
                $this->packageFile->getPath(),
                $directory
            ));

            $commands->merge(
                $this->getCommandsForDirectory($namespace, $commandDirectory)
            );
        }

        return $commands;
    }
}
