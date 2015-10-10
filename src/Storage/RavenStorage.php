<?php namespace SoapBox\Raven\Storage;

class RavenStorage extends WritableStorage
{
	protected function __construct()
	{
		$this->loadFile(soapbox_path() . '/.ravendata');
	}
}
