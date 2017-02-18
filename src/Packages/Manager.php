<?php

namespace SoapBox\Raven\Packages;

use SplFileInfo;
use CallbackFilterIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Composer\Autoload\ClassLoader;
use SoapBox\Raven\Utilities\Collection;

class Manager
{
    const PACKAGE_DIRECTORY = '/usr/local/raven/packages';

    /**
     * The class loader instance
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * Create a new package manager
     *
     * @param \Composer\Autoload\ClassLoader $loader
     *        The class loader instance
     */
    public function __construct(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get a collection of all the installed packages
     *
     * @return \SoapBox\Raven\Utilities\Collection
     */
    public function getPackages(): Collection
    {
        $packageDir = new SplFileInfo(self::PACKAGE_DIRECTORY);
        $packages = new Collection();

        $files = new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($packageDir->getPathname())
            ),
            function ($current) {
                return preg_match('/raven.json$/', $current->getFilename());
            }
        );

        foreach ($files as $packageFile) {
            $packages->push(new Package($packageFile, $this->loader));
        }

        return $packages;
    }
}
