<?php namespace SoapBox\Raven\Storage;

abstract class ReadableStorage
{
	private static $instance;

	private $fileLoaded = false;
	protected $data = [];
	protected $dataFile;

	protected function __construct() {}

	protected function loadFile($filePath)
	{
		$this->dataFile = $filePath;

		if (file_exists($filePath)) {
			$fileLoaded = true;
			$this->data = json_decode(file_get_contents($this->dataFile), true);
		}
	}

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

	public function has($key)
	{
		return !is_null($this->get($key));
	}

	public function exists()
	{
		return $this->fileLoaded;
	}

	public static function getStorage()
	{
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}
}
