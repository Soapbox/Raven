<?php namespace SoapBox\Raven\Commands;

use Exception;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use SoapBox\Raven\Utils\Command;
use SoapBox\Raven\Utils\SelfUpdater;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class SelfUpdateCommand extends Command
{
    const MANIFEST = 'https://soapbox.github.io/raven/manifest.json';

    protected $command = 'self-update';
    protected $description = 'TUpdates raven to the latest version';

    protected function addArguments()
    {

    }

    protected function addOptions()
    {
        $this->makeOption('pre')
            ->setDescription('Allow updating to pre-releases')
            ->boolean();

        $this->makeOption('major')
            ->setDescription('Allow updating major releases')
            ->boolean();
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
        chRootDir();

        $selfUpdater = new SelfUpdater($this->getApplication());
        $major = $input->getOption('major');
        $pre = $input->getOption('pre');
        $update = $selfUpdater->getUpdate($major, $pre);

        if (is_null($update)) {
            $output->writeln(sprintf('<info>%s</info> is up to date.', $this->getApplication()->getName()));
            return;
        }

        if ($update->isNewer($selfUpdater->getVersion())) {
            $output->writeln(sprintf('New version of <info>%s</info> available.', $this->getApplication()->getName()));
            $output->writeln(sprintf(
                'Updating from <comment>%s</comment> to <comment>%s</comment>',
                $this->getApplication()->getVersion(),
                $update->getVersion()
            ));

            $commandInput = new ArrayInput([
                '--batch' => true,
                'starting_tag' => $this->getApplication()->getVersion(),
                'ending_tag' => $update->getVersion()
            ]);

            $currentDir = getcwd();
            $changeLogCommand = $this->getApplication()->find('generate-changelog');
            try {
                chRootDir();
                $changeLogCommand->run($commandInput, $output);
            } catch (Exception $e) {

            }

            chdir($currentDir);
            $selfUpdater->update($major, $pre);

            $output->writeln(sprintf(
                '<info>%s</info> was successfully updated to version <comment>%s</comment>',
                $this->getApplication()->getName(),
                $update->getVersion()
            ));
        }
    }
}
