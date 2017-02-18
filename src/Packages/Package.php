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
    /**
     * The raven.json file for the package
     *
     * @var \SplFileInfo
     */
    private $packageFile;

    /**
     * The collection of data from that package file
     *
     * @var \SoapBox\Raven\Utilities\Collection
     */
    private $fileData;

    /**
     * The class loader instance
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * Create a new package
     *
     * @param \SplFileInfo $packageFile
     *        The raven.json file for the package
     * @param \Composer\Autoload\ClassLoader $loader
     *        The class loader instance
     */
    public function __construct(SplFileInfo $packageFile, ClassLoader $loader)
    {
        $this->packageFile = $packageFile;
        $this->loader = $loader;

        $file = $packageFile->openFile();
        $this->fileData = new Collection(
            json_decode($file->fread($file->getSize()), true)
        );
    }

    /**
     * Get the command namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->fileData->get('command_namespace');
    }

    /**
     * Get the commands that are in the given directory
     *
     * @param string $namespace
     *        The namespace of the classes in the given directory. This should
     *        follow PSR4
     * @param \SplFileInfo $directory
     *        The directory to commands from
     *
     * @return \SoapBox\Raven\Utilities\Collection
     */
    private function getCommandsForDirectory(string $namespace, SplFileInfo $directory): Collection
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

    /**
     * Get all the commands for this package
     *
     * @return \SoapBox\Raven\Utilities\Collection
     */
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
