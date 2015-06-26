<?php namespace SoapBox\SoapboxVagrant;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('init')
                  ->setDescription('Create a stub Soapbox.yaml file');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_dir(soapbox_path())) {
            throw new \InvalidArgumentException("SoapBox has already been initialized.");
        }

        mkdir(soapbox_path());

        copy(__DIR__.'/stubs/Soapbox.yaml', soapbox_path().'/Soapbox.yaml');
        copy(__DIR__.'/stubs/after.sh', soapbox_path().'/after.sh');
        copy(__DIR__.'/stubs/aliases', soapbox_path().'/aliases');

        $output->writeln('<comment>Creating Soapbox.yaml file...</comment> <info>✔</info>');
        $output->writeln('<comment>Soapbox.yaml file created at:</comment> '.soapbox_path().'/Soapbox.yaml');
    }
}
