<?php namespace SoapBox\Raven\ChangeLog;

use SoapBox\Raven\GitHub\PullRequest;
use SoapBox\Raven\Storage\ProjectStorage;
use Raven\Api\ChangeLog\SectionEntry as SectionEntryInterface;

class SectionEntry implements SectionEntryInterface
{
    private $pullRequest;
    private $title;
    private $subText = [];

    public function __construct(PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;

        $storage = ProjectStorage::getStorage();
        $sections = $storage->get('changelog.sections', []);

        if (!empty($sections)) {
            $labels = implode('|', array_keys($sections));
            $title = preg_replace('/\[(?:'.$labels.')\]/i', '', $pullRequest->getTitle());
        }

        $title = trim($title);
        $title = sprintf('%s [#%s]', $title, $pullRequest->getNumber());
        $this->setTitle($title);
    }

    /**
     * Get the PullRequests associated with this SectionEntry
     *
     * @return Raven\Api\GitHub\PullRequest
     */
    public function getPullRequest()
    {
        return $this->pullRequest;
    }

    /**
     * Get the title for this SectionEntry
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title for this SectionEntry
     *
     * @param string $title The title to set for this SectionEntry
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
