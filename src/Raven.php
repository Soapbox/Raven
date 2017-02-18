<?php

namespace SoapBox\Raven;

use SplFileInfo;
use Composer\Autoload\ClassLoader;
use SoapBox\Raven\Packages\Command;
use SoapBox\Raven\Packages\Manager;
use Symfony\Component\Console\Application;

class Raven extends Application
{
    /**
     * The class loader instance
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private $loader;

    /**
     * Create a new application
     *
     * @param \Composer\Autoload\ClassLoader $loader
     *        The class loader instance
     */
    public function __construct(ClassLoader $loader)
    {
        parent::__construct('Raven', '2.0.0');

        $this->loader = $loader;
        $this->registerCommands();
    }

    /**
     * Register the raven commands with this application
     *
     * @return void
     */
    private function registerCommands(): void
    {
        $dir = __DIR__ . '/Commands';
        $files = scandir($dir);

        foreach ($files as $file) {
            if (is_file($dir . '/' . $file)) {
                $class = sprintf('SoapBox\Raven\Commands\%s', rtrim($file, '.php'));
                $this->add(new $class);
            }
        }

        $this->registerPackageCommands();
    }

    /**
     * Register the package commands with this application
     *
     * @return void
     */
    private function registerPackageCommands(): void
    {
        $manager = new Manager($this->loader);
        $packages = $manager->getPackages();

        foreach ($packages as $package) {
            foreach ($package->getCommands() as $command) {
                $this->add(new Command($package->getNamespace(), $command));
            }
        }
    }
}
