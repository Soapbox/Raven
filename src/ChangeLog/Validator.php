<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\SectionEntry;
use Raven\Api\ChangeLog\Validator as ValidatorInterface;

class Validator implements ValidatorInterface
{
	public function __construct(array $sections)
	{
		$this->sections = $sections;
	}

	public function isValid(SectionEntry $entry)
	{
		return true;
		$title = $entry->getPullRequest()->getTitle();

		$matches = [];
		return preg_match('/^\[(\w+)\]/', $title, $matches) && array_key_exists(strtolower($matches[1]), $this->sections);
	}
}
