<?php namespace SoapBox\Raven\Utils;

use Symfony\Component\Console\Input\InputOption;

class Option
{
	private $default;
	private $description;
	private $mode;
	private $name;
	private $shortcuts = [];

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDefault($default)
	{
		$this->default = $default;
		return $this;
	}

	public function getDefault()
	{
		return $this->default;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setMode($mode)
	{
		$this->mode = $mode;
		return $this;
	}

	public function getMode()
	{
		return $this->mode;
	}

	public function addShortcut($shortcut)
	{
		$this->shortcuts[] = $shortcut;
		return $this;
	}

	public function getShortcut()
	{
		return $this->shortcuts;
	}

	public function get()
	{
		return new InputOption(
			$this->name,
			$this->shortcuts,
			$this->mode,
			$this->description,
			$this->default
		);
	}

	public function optional() {
		$this->setMode(InputOption::VALUE_OPTIONAL);
		return $this;
	}

	public function required() {
		$this->setMode(InputOption::VALUE_REQUIRED);
		return $this;
	}

	public function boolean() {
		$this->setMode(InputOption::VALUE_NONE);
		return $this;
	}

	public function isArray() {
		$this->setMode(InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL);

		if (is_null($this->default)) {
			$this->default = [];
		}
		return $this;
	}
}
