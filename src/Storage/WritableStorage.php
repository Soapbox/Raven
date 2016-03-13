<?php namespace SoapBox\Raven\Storage;

abstract class WritableStorage extends ReadableStorage
{
    /**
     * Save the current data to the file
     */
    private function save()
    {
        file_put_contents($this->dataFile, json_encode($this->data));
    }

    /**
     * Set a value in this storage and save it to the file
     *
     * @param string $key   The key in the storage to save the value
     * @param mixed  $value The value to set
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        $this->save();
    }
}
