<?php namespace SoapBox\Raven\Utils;

class HelpSection {
	private $title;
	private $content;

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getContent() {
		return $this->content;
	}

	public function addContent($line) {
		if (!empty($this->content)) {
			$this->content .= "\n";
		}

		$this->content .= $line;
	}
}