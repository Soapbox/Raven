<?php namespace SoapBox\Raven\Storage;

abstract class WritableStorage extends ReadableStorage
{
	private function save()
	{
		file_put_contents($this->dataFile, json_encode($this->data));
	}
}
