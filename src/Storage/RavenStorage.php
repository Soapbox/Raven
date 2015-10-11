<?php namespace SoapBox\Raven\Storage;

class RavenStorage extends WritableStorage
{
	protected static $instance;

	protected function __construct()
	{
		$this->loadFile(soapbox_path() . '/.ravendata');
	}
}
