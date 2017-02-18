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
    private $loader;

    public function __construct(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    public function getPackages(SplFileInfo $packageDir): Collection
    {
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
