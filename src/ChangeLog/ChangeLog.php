<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\ChangeLog as ChangeLogInterface;

class ChangeLog implements ChangeLogInterface {
	private $title;
	private $sections;

	public function __construct() {
		$this->sections = new SectionCollection();
	}

	/**
	 * Get the title text that appears at the top of the ChangeLog
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set the title text for this ChangeLog
	 *
	 * @param string $title The title text to set
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Get the collection of Sections for this ChangeLog
	 *
	 * @return Raven\Api\ChangeLog\SectionCollection
	 */
	public function getSections() {
		return $this->sections;
	}

	/**
	 * Add a section to this ChangeLog
	 *
	 * @param Section $section The section to add
	 */
	public function addSection(Section $section) {
		$this->sections->push($section);
	}
}
