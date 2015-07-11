<?php namespace SoapBox\SoapboxVagrant\Utils;

use Herrera\Phar\Update\Manifest;
use KevinGH\Version\Version;
use Symfony\Component\Console\Application;

class SelfUpdater {
	const MANIFEST = 'manifest.json';// 'https://soapbox.github.io/raven/manifest.json';

	private $application;
	private $manifest;

	public function __construct(Application $application) {
		$this->application = $application;
	}

	private function getManifest() {
		if (is_null($this->manifest)) {
			$this->manifest = Manifest::loadFile(self::MANIFEST);
		}

		return $this->manifest;
	}

	public function getUpdate($major = true, $pre = false) {
		$manifest = $this->getManifest();
		$version = Version::create(this->application->getVersion());

		return $manifest->findRecent($version, $major, $pre);
	}

	public function update($major = true, $pre = false) {
		$manager = new Manager($this->getManifest());
		$manager->update($this->getApplication()->getVersion(), true);
	}
}
