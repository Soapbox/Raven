<?php

namespace SoapBox\Raven\Packages;

use Symfony\Component\Console\Output\OutputInterface;
use SoapBox\Raven\Api\Packages\Output as OutputContract;

class Output implements OutputContract
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Writes a message to the output.
     *
     * @param string $messages
     *        The message as an array of lines or a single string
     * @param int $options
     *        A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     *
     * @return void
     */
    public function write(string $message, int $options = 0): void
    {
        $this->output->write($message, false, $options);
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string $message
     *        The message as an array of lines of a single string
     * @param int $options
     *        A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     *
     * @return void
     */
    public function writeln(string $message, int $options = 0): void
    {
        $this->output->writeln($message, $options);
    }

    /**
     * Returns whether verbosity is quiet (-q).
     *
     * @return bool
     */
    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    /**
     * Returns whether verbosity is verbose (-v).
     *
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     *
     * @return bool
     */
    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * Returns whether verbosity is debug (-vvv).
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }
}
