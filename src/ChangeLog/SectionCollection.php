<?php namespace SoapBox\Raven\ChangeLog;

use InvalidArgumentException;
use Raven\Api\ChangeLog\SectionCollection as SectionCollectionInterface;
use SoapBox\Raven\Utils\Collection;

class SectionCollection extends Collection implements SectionCollectionInterface {
	public function push($item) {
		if (!$item instanceof Section) {
			throw new InvalidArgumentException('Trying to add a non Section');
		}

		parent::push($item);
	}
}
