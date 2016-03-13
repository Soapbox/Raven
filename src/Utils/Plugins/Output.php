<?php

namespace SoapBox\Raven\Utils\Plugins;

use Raven\Api\Command\Output as RavenOutput;
use Symfony\Component\Console\Output\Output as SymfonyOutput;

class Output implements RavenOutput
{
    private $output;

    public function __construct(SymfonyOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages
     *        The message as an array of lines or a single string
     *
     * @return void
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function write($messages)
    {
        $this->output->write($messages);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages
     *        The message as an array of lines of a single string
     *
     * @return void
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function writeln($messages)
    {
        $this->output->writeln($messages);
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return int
     *         The current level of verbosity
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }
}
