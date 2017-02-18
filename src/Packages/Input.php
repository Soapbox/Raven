<?php

namespace SoapBox\Raven\Packages;

use Symfony\Component\Console\Input\InputInterface;
use SoapBox\Raven\Api\Packages\Input as InputContract;

class Input implements InputContract
{
    /**
     * The decorated InputInterface instance
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * Create a new Input object
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *        The underlying input instance
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Returns the argument value for a given argument name.
     *
     * @throws \InvalidArgumentException
     *         When argument given doesn't exist
     *
     * @param string $name
     *        The argument name
     *
     * @return mixed
     */
    public function getArgument(string $name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * Returns true if an argument exists by name or position.
     *
     * @param string $name
     *        The argument name
     *
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        $this->input->hasArgument($name);
    }

    /**
     * Returns the option value for a given option name.
     *
     * @throws \InvalidArgumentException
     *         When option given doesn't exist
     *
     * @param string $name
     *        The option name
     *
     * @return mixed
     */
    public function getOption(string $name)
    {
        return $this->input->getOption($name);
    }

    /**
     * Returns true if an option exists by name.
     *
     * @param string $name
     *        The option name
     *
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }
}
