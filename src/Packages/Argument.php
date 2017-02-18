<?php

namespace SoapBox\Raven\Packages;

class Argument
{
    private $name;
    private $mode;
    private $description;
    private $default;

    public function __construct(string $name, int $mode, string $description, $default)
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->description = $description;
        $this->default = $default;
    }

    public function getName(): string
    {
        return $this->name;
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
