<?php

namespace SoapBox\Raven\Packages;

use SoapBox\Raven\Api\Packages\Configuration as ConfigurationContract;

class Configuration implements ConfigurationContract
{
    private $arguments = [];
    private $options = [];
    private $name = '';
    private $description = '';
    private $help = '';

    public function setName(string $name): ConfigurationContract
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): ConfigurationContract
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setHelp(string $help): ConfigurationContract
    {
        $this->help = $help;
        return $this;
    }

    public function getHelp(): string
    {
        return $this->help;
    }

    public function addArgument(
        string $name,
        int $mode = ConfigurationContract::ARGUMENT_OPTIONAL,
        string $description = '',
        $default = null
    ): ConfigurationContract {
        $this->arguments[] = new Argument($name, $mode, $description, $default);
        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addOption(
        string $name,
        string $shortcut = '',
        int $mode = self::OPTION_VALUE_NONE,
        string $description = '',
        $default = null
    ): ConfigurationContract {
        $this->options[] = new Option($name, $shortcut, $mode, $description, $default);
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
