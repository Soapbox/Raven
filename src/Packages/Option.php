<?php

namespace SoapBox\Raven\Packages;

class Option
{
    private $name;
    private $shortcut;
    private $mode;
    private $description;
    private $default;

    public function __construct(string $name, string $shortcut, int $mode, string $description, $default)
    {
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortcut(): string
    {
        return $this->shortcut;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
