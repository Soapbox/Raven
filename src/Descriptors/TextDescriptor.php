<?php namespace SoapBox\Raven\Descriptors;

use SoapBox\Raven\Utils\Command as RavenCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\TextDescriptor as Descriptor;

class TextDescriptor extends Descriptor
{
    protected function describeCommand(Command $command, array $options = array())
    {
        parent::describeCommand($command, $options);

        if ($command instanceof RavenCommand) {
            $sections = $command->getHelpSections();

            foreach ($sections as $section) {
                $this->writeText("\n");
                $this->writeText(sprintf('<comment>%s:</comment>', $section->getTitle()), $options);
                $this->writeText("\n");
                $this->writeText('  '.str_replace("\n", "\n  ", $section->getContent()), $options);
                $this->writeText("\n");
            }
        }
    }

    private function writeText($content, array $options = array())
    {
        $this->write(
            isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
            isset($options['raw_output']) ? !$options['raw_output'] : true
        );
    }
}
