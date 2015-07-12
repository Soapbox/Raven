<?php namespace SoapBox\SoapboxVagrant\Utils;

use Herrera\Phar\Update\Manifest;
use KevinGH\Version\Version;
use Symfony\Component\Console\Application;
use Symfony\Component\Process\Process;

class SelfUpdater {
	const MANIFEST = 'manifest.json';

	private $application;
	private $manifest;
	private $update;

	public function __construct(Application $application) {
		$this->application = $application;
	}

	private function getManifest() {
		if (is_null($this->manifest)) {
			$this->manifest = Manifest::loadFile(self::MANIFEST);
		}

		return $this->manifest;
	}

	private function getVersion() {
		if (is_null($this->version)) {
			$this->version = new Version($this->application->getVersion());
		}

		return $this->version;
	}

	private function run($command, $callback = null) {
		$process = new Process($command, realpath(__DIR__ . '/../../'), array_merge($_SERVER, $_ENV));
		$process->run($callback);
	}

	public function getUpdate($major = true, $pre = false) {
		if (is_null($this->update)) {
			$this->run('git fetch --all');
			$this->run('git checkout origin/releases -- manifest.json');

			$manifest = $this->getManifest();
			$this->update = $manifest->findRecent($this->getVersion(), $major, $pre);

			$this->run('rm manifest.json');
		}

		return $this->update;
	}

	public function update($major = true, $pre = false) {
		$update = $this->getUpdate();

		if ( !is_null($update) && $update->isNewer($this->getVersion()) ) {
			$newVersion = $update->getVersion();
			$file = $update->getUrl();

			$this->run("git checkout origin/releases -- releases/$file");
			$this->run("mv releases/$file $file; rm -r releases");

			$update->getFile();

			$this->run("rm $file");

			$update->copyTo(realpath($_SERVER['argv'][0]));

			return true;
		}

		return false;
	}
}
