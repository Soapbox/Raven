<?php namespace SoapBox\Raven\ChangeLog;

use InvalidArgumentException;
use Raven\Api\ChangeLog\EntryCollection as EntryCollectionInterface;
use SoapBox\Raven\Utils\Collection;

class EntryCollection extends Collection implements EntryCollectionInterface {
	public function push($item) {
		if ($item instanceof Entry) {
			throw new InvalidArgumentException('Trying to add a non Entry');
		}

		parent::push($item);
	}
}
