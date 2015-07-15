<?php namespace SoapBox\Raven;

use SoapBox\Raven\Commands;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Raven extends Application {
	public function __construct($name = 'Raven', $version = '@version@')
	{
		parent::__construct($name, $version);
		$this->registerCommands();
	}

	private function registerCommands()
	{
		$this->add(new Commands\ClearCacheCommand);
		$this->add(new Commands\DestroyCommand);
		$this->add(new Commands\EditCommand);
		$this->add(new Commands\HaltCommand);
		$this->add(new Commands\InitCommand);
		$this->add(new Commands\MakeCommand);
		$this->add(new Commands\ProvisionCommand);
		$this->add(new Commands\RebuildCommand);
		$this->add(new Commands\RefreshCommand);
		$this->add(new Commands\ResumeCommand);
		$this->add(new Commands\RunCommand);
		$this->add(new Commands\SelfUpdateCommand);
		$this->add(new Commands\SshCommand);
		$this->add(new Commands\StatusCommand);
		$this->add(new Commands\SuspendCommand);
		$this->add(new Commands\UpCommand);
		$this->add(new Commands\UpdateCommand);
	}
	
	public function run(InputInterface $input = null, OutputInterface $output = null)
	{
		return parent::run($input, $output);
	}
}
