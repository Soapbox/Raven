<?php namespace SoapBox\Raven\Storage;

abstract class ReadableStorage
{
    private $fileLoaded = false;
    protected $data = [];
    protected $dataFile;

    protected function __construct()
    {

    }

    /**
     * Load the file's data
     *
     * @param string $filePath The file to load
     */
    protected function loadFile($filePath)
    {
        $this->dataFile = $filePath;

        if (file_exists($filePath)) {
            $this->fileLoaded = true;
            $this->data = json_decode(file_get_contents($this->dataFile), true);
        }
    }

    /**
     * Get an element from the stoarage
     *
     * @param string $key     The key for which to search the storage
     * @param mized  $default The deafault value for when the key is not found
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $keys = explode('.', $key);

        $data = $this->data;
        foreach ($keys as $key) {
            if (!is_array($data) || !array_key_exists($key, $data)) {
                return $default;
            }

            $data = $data[$key];
        }

        return $data;
    }

    /**
     * Check to see if the storage contains a value
     *
     * @param string $key The key to check
     * @return bool
     */
    public function has($key)
    {
        return !is_null($this->get($key));
    }

    /**
     * Get whether or not the storage file exists
     *
     * @return bool
     */
    public function exists()
    {
        return $this->fileLoaded;
    }

    /**
     * Get the singleton instance of the storage
     *
     * @return ReadableStorage
     */
    public static function getStorage()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
