<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\Section as SectionInterface;

class Section implements SectionInterface
{
	private $entries;
	private $title;

	public function __construct($title)
	{
		$this->entries = new EntryCollection();
		$this->title = $title;
	}

	/**
	 * Get the entries for this section
	 *
	 * @return Raven\Api\ChangeLog\EntryCollection
	 */
	public function getEntries()
	{
		return $this->entries;
	}

	/**
	 * Get the title for this Section
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Add an entry to this section
	 *
	 * @param SectionEntry $entry The entry to add
	 */
	public function addEntry(SectionEntry $entry) {
		$this->entries->push($entry);
	}
}
