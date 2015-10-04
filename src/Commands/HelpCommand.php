<?php namespace SoapBox\Raven\Commands;

use Symfony\Component\Console\Command\HelpCommand as BaseCommand;
use Symfony\Component\Console\Command\Command;
use SoapBox\Raven\Descriptors\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends BaseCommand
{
    private $command;

    /**
     * Sets the command.
     *
     * @param Command $command The command to set
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->command) {
            $this->command = $this->getApplication()->find($input->getArgument('command_name'));
        }

        if ($input->getOption('xml')) {
            @trigger_error('The --xml option was deprecated in version 2.7 and will be removed in version 3.0. Use the --format option instead.', E_USER_DEPRECATED);

            $input->setOption('format', 'xml');
        }

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, array(
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ));

        $this->command = null;
    }
}
