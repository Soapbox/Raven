<?php namespace SoapBox\Raven\Utils;

use Symfony\Component\Console\Input\InputArgument;

class Argument
{
	private $default;
	private $description;
	private $mode;
	private $name;

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

	public function addMode($mode)
	{
		if (is_null($this->mode)) {
			$this->mode = $mode;
		} else {
			$this->mode |= $mode;
		}
		return $this;
	}

	public function getMode()
	{
		return $this->mode;
	}

	public function get()
	{
		return new InputArgument(
			$this->name,
			$this->mode,
			$this->description,
			$this->default
		);
	}

	public function optional() {
		$this->addMode(InputArgument::OPTIONAL);
		return $this;
	}

	public function required() {
		$this->addMode(InputArgument::REQUIRED);
		return $this;
	}

	public function isArray() {
		$this->addMode(InputArgument::IS_ARRAY);
		return $this;
	}
}
