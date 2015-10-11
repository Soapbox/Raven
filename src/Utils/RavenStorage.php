<?php namespace SoapBox\Raven\Utils;

use LogicException;

class RavenStorage {
	private $data = [];
	private $dataFile;
	private static $instance;

	private function __construct() {
		$this->dataFile = soapbox_path() . '/.ravendata';

		if (file_exists($this->dataFile)) {
			$this->data = json_decode(file_get_contents($this->dataFile), true);
		}
	}

	private function save() {
		file_put_contents($this->dataFile, json_encode($this->data));
	}

	public function get($key) {
		if ( !array_key_exists($key, $this->data) ) {
			return '';
		}
		return $this->data[$key];
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
		$this->save();
	}

	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	public static function getStorage() {
		if (is_null(self::$instance)) {
			self::$instance = new RavenStorage();
		}
		return self::$instance;
	}
}
