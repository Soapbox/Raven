<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\SectionCollectionInterface;
use SoapBox\Raven\Utils\Collection;

class SectionCollection extends Collection implements SectionCollectionInterface {
	public function push($item) {
		if ($item instanceof Section) {
			parent::push($item);
		}

		throw new InvalidArgumentException('Trying to add a non Section');
	}
}
