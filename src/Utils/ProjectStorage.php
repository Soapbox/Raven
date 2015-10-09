<?php namespace SoapBox\Raven\Utils;

use LogicException;
use RuntimeException;
use SoapBox\Raven\Helpers\GitHelper;
use Symfony\Component\Process\Process;

class ProjectStorage {
	private $data = [];
	private $dataFile;
	private static $instance;

	private function __construct() {
		$file = getcwd() . '/raven.json';
		if (!file_exists($file)) {
			try {
				$root = GitHelper::getRepositoryRoot();

				$file = $root  . '/raven.json';
			} catch (RuntimeException $e) {}
		}

		if (file_exists($file)) {
			$this->dataFile = $file;
			$this->data = json_decode(file_get_contents($this->dataFile), true);
		}
	}

	public function get($key) {
		$keys = explode('.', $key);

		$data = $this->data;
		foreach ($keys as $key) {
			if (!is_array($data) || !array_key_exists($key, $data)) {
				return null;
			}

			$data = $data[$key];
		}

		return $data;
	}

	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	public static function getStorage() {
		if (is_null(self::$instance)) {
			self::$instance = new ProjectStorage();
		}
		return self::$instance;
	}
}
