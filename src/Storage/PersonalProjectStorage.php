<?php namespace SoapBox\Raven\Storage;

use RuntimeException;
use SoapBox\Raven\Helpers\GitHelper;

class PersonalProjectStorage extends WritableStorage
{
    protected function __construct()
    {
        $storage = ProjectStorage::getStorage();
        if ($storage->exists()) {
            $file = sprintf('%s/.ravendata_%s', soapbox_path(), hash('sha256', $storage->getProjectRoot()));
            $this->loadFile($file);
        }
    }
}
