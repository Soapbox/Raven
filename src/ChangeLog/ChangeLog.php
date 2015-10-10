<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\ChangeLog as ChangeLogInterface;

class ChangeLog implements ChangeLogInterface
{
	private $title;
	private $sections;
	private $previousVersion;
	private $currentVersion;

	public function __construct($previousVersion, $currentVersion)
	{
		$this->previousVersion = $previousVersion;
		$this->currentVersion = $currentVersion;
		$this->setTitle(sprintf('Changes from %s to %s', $previousVersion, $currentVersion));
		$this->sections = new SectionCollection();
	}

	/**
	 * Get the title text that appears at the top of the ChangeLog
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set the title text for this ChangeLog
	 *
	 * @param string $title The title text to set
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Get the collection of Sections for this ChangeLog
	 *
	 * @return Raven\Api\ChangeLog\SectionCollection
	 */
	public function getSections()
	{
		return $this->sections;
	}

	/**
	 * Add a section to this ChangeLog
	 *
	 * @param Section $section The section to add
	 */
	public function addSection(Section $section)
	{
		$this->sections->push($section);
	}

	/**
	 * Add a section to this ChangeLog
	 *
	 * @param string  $key     The key for the Section
	 * @param Section $section The section to add
	 */
	public function addSectionByKey($key, Section $section)
	{
		$this->sections->add($key, $section);
	}

	/**
	 * Get the version from which this change log is generated
	 *
	 * @return string
	 */
	public function getPreviousVersion()
	{
		return $this->previousVersion;
	}

	/**
	 * Get the version to which change log is generated
	 *
	 * @return string
	 */
	public function getCurrentVersion()
	{
		return $this->currentVersion;
	}
}
