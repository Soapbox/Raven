<?php namespace SoapBox\Raven\ChangeLog;

use Raven\Api\ChangeLog\EntryCollection as EntryCollectionInterface;
use SoapBox\Raven\Utils\Collection;

class EntryCollection extends Collection implements EntryCollectionInterface {
	public function push($item) {
		if ($item instanceof Entry) {
			parent::push($item);
		}

		throw new InvalidArgumentException('Trying to add a non Section');
	}
}
