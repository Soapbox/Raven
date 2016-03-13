<?php

namespace SoapBox\Raven\Utils\Plugins;

use Raven\Api\Command\Input as RavenInput;
use Symfony\Component\Console\Input\Input as SymfonyInput;

class Input implements RavenInput
{
    private $input;

    public function __construct(SymfonyInput $input)
    {
        $this->input = $input;
    }

    /**
     * Returns all the given arguments merged with the default values.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->input->getArguments();
    }

    /**
     * Gets argument by name.
     *
     * @param string $name
     *        The name of the argument
     *
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
     * @param string|int $name
     *        The InputArgument name or position
     *
     * @return boolean
     *         true if the InputArgument object exists, false otherwise
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Returns all the given options merged with the default values.
     *
     * @return array
     */
    public function getOptions()
    {
        $this->input->getOptions();
    }

    /**
     * Gets an option by name.
     *
     * @param string $name
     *         The name of the option
     *
     * @return mixed
     */
    public function getOption($name)
    {
        $this->input->getOption($name);
    }

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @param string $name
     *        The InputOption name
     *
     * @return boolean
     *         true if the InputOption object exists, false otherwise
     */
    public function hasOption($name)
    {
        $this->input->hasOption($name);
    }

    /**
     * Is this input means interactive?
     *
     * @return boolean
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }
}
