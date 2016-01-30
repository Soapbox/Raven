<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\SectionEntry as SectionEntryInterface;
use SoapBox\Raven\GitHub\PullRequest;

class SectionEntry implements SectionEntryInterface
{
	use FormatTrait;

	private $pullRequest;
	private $title;
	private $subText = [];

	public function __construct(PullRequest $pullRequest)
	{
		$this->pullRequest = $pullRequest;
		$title = trim(preg_replace('/^\[.*\]/', '', $pullRequest->getTitle()));
		$title = sprintf('      %s [#%s]', $title, $pullRequest->getNumber());
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

	/**
	 * Get the sub text for this SectionEntity
	 *
	 * @return array
	 */
	public function getSubText()
	{
		return $this->subText;
	}

	/**
	 * Add sub text to this SectionEntity
	 *
	 * @param string $text The sub text to add
	 */
	public function addSubText($text)
	{
		$this->subText[] = $text;
	}

	/**
	 * Set the sub text for this SectionEntity
	 *
	 * @param string $text The sub text to set
	 */
	public function setSubText($text)
	{
		$this->subText = $text;
	}

	public function __toString()
	{
		$result = $this->formatLine($this->getTitle());

		foreach ($this->getSubText() as $subtext) {
			$result .= $this->formatLine('- '.$subtext, true);
		}

		return trim($result, " \r\n");
	}
}
