<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\SectionEntry as SectionEntryInterface;
use SoapBox\Raven\GitHub\PullRequest;

class SectionEntry implements SectionEntryInterface
{
	private $pullRequest;
	private $text;

	public function __construct(PullRequest $pullRequest)
	{
		$this->pullRequest = $pullRequest;
		$title = trim(preg_replace('/^\[.*\]/', '', $pullRequest->getTitle()));
		$this->setText($title);
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
	 * Get the text for this SectionEntry
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * Set the text for this SectionEntry
	 *
	 * @param string $text The text to set for this SectionEntry
	 */
	public function setText($text)
	{
		$this->text = $text;
	}
}
