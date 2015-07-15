<?php namespace SoapBox\Raven\Utils;

use Closure;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class Command extends SymfonyCommand
{
	protected $command = '';
	protected $description = '';
	private $options = [];
	private $arguments = [];

	protected function configure()
	{
		$this->setName($this->command);
		$this->setDescription($this->description);

		$this->addArguments();
		$this->addOptions();

		foreach ($this->options as $option)
		{
			$this->addOption(
				$option->getName(),
				$option->getShortcut(),
				$option->getMode(),
				$option->getDescription(),
				$option->getDefault()
			);
		}

		foreach ($this->arguments as $argument)
		{
			$this->addArgument(
				$argument->getName(),
				$argument->getMode(),
				$argument->getDescription(),
				$argument->getDefault()
			);
		}
	}

	protected function addArguments() {}
	protected function addOptions() {}

	protected function makeOption($name)
	{
		$option = new Option($name);
		$this->options[] = $option;
		return $option;
	}
}
