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

    public function getPrefix(): string
    {
        return $this->fileData->get('prefix');
    }

    public function getCommands(): Collection
    {
        $commands = new Collection();

        $commandDirectory = new SplFileInfo(sprintf(
            '%s/%s',
            $this->packageFile->getPath(),
            $this->get('command_path')
        ));

        $this->loader->addPsr4($this->getNamespace(), $commandDirectory->getPathname());

        $commandFiles = new CallbackFilterIterator(
            new FilesystemIterator($commandDirectory),
            function ($current) {
                return $current->getExtension() === 'php';
            }
        );

        foreach ($commandFiles as $commandFile) {
            $class = sprintf(
                '%s\\%s',
                rtrim($this->getNamespace(), '\\'),
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
}
