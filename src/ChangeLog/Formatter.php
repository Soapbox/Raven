<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\ChangeLog;
use Raven\Api\ChangeLog\Formatter as FormatterInterface;

class Formatter extends FormatterInterface
{
    public function format(ChangeLog $changeLog)
    {
        $changeLog->setTitle(sprintf('<info>%s</info>', $changeLog->getTitle()));

        foreach ($this->changeLog->getSections() as $section) {
            if ($section->getEntries()->isEmpty()) {
                continue;
            }

            $changeLog->setTitle(sprintf('   <comment>%s</comment>', $section->getTitle()));
            foreach ($section->getEntries() as $entry) {
                $entry->setTitle(sprintf('      %s #%s', $entry->getTitle(), $entry->getPullRequest()->getNumber()));

                $subText = [];
                foreach ($entry->getSubText() as $subText) {
                    $subText[] = sprintf('         %s', $subText);
                }
                $entry->setSubText($subText);
            }
        }
    }
}
