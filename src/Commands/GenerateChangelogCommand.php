<?php namespace SoapBox\Raven\Commands;

use InvalidArgumentException;
use RuntimeException;
use SoapBox\Raven\ChangeLog\ChangeLog;
use SoapBox\Raven\ChangeLog\Section;
use SoapBox\Raven\ChangeLog\SectionEntry;
use SoapBox\Raven\GitHub\Client;
use SoapBox\Raven\GitHub\PullRequest;
use SoapBox\Raven\Utils\Command;
use SoapBox\Raven\Utils\ProjectStorage;
use SoapBox\Raven\Utils\RavenStorage;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateChangelogCommand extends Command {
	private $sections = [
		'misc' => []
	];

	private $sectionLabels = [
		'misc' => 'Other'
	];

	protected $command = 'generate-changelog';
	protected $description = 'Generate a changelog for the current repo';

	protected function addArguments() {
		$this->makeArgument('starting_tag')
			->setDescription('The tag to start looking for pull requests.')
			->setDefault('')
			->optional();

		$this->makeArgument('ending_tag')
			->setDescription('The tag to finish looking for pull requests.')
			->setDefault('')
			->optional();
	}

	protected function addOptions() {}

	private function exec($command) {
		$output = [];
		$returnStatus = 0;

		exec($command, $output, $returnStatus);

		if ($returnStatus !== 0) {
			throw new RuntimeException(sprintf('Failed executing "%s".', $command));
		}

		return $output;
	}

	private function getAccessToken() {
		$storage = RavenStorage::getStorage();

		if ($accessToken = $storage->get('github_access_token')) {
			$email = $this->exec('git config --global user.email')[0];

			$question = new Question(sprintf('Enter host password for user \'%s\':', $email));
			$question->setHidden(true);
			$question->setHiddenFallback(false);

			$helper = $this->getHelper('question');
			$password = $helper->ask($input, $output, $question);

			$accessToken = $this->client->acquireAccessToken($email, $password);
			$storage->set('github_access_token', $accessToken);
		}

		$this->client->setAccessToken($accessToken);
		return $accessToken;
	}

	private function getReleaseTags(InputInterface $input) {
		$tags = $this->exec('git tag');
		natsort($tags);
		$tags = array_values($tags);
		$recentTags = array_slice($tags, count($tags) - 2);

		$startingTag = $input->getArgument('starting_tag');
		$endingTag = $input->getArgument('ending_tag');

		if (empty($startingTag)) {
			$startingIndex = array_search($recentTags[0], $tags);
			$startingTag = $tags[$startingIndex];
		} else if (false === $startingIndex = array_search($startingTag, $tags)) {
			throw new InvalidArgumentException('The starting_tag argument is not a valid tag.');
		}

		if (empty($endingTag)) {
			$endingIndex = array_search($recentTags[1], $tags);
			$endingTag = $tags[$endingIndex];
		} else if (false === $endingIndex = array_search($endingTag, $tags)) {
			throw new InvalidArgumentException('The ending_tag argument is not a valid tag.');
		}

		if ($startingIndex >= $endingIndex) {
			throw new InvalidArgumentException('The starting_tag must be less than the ending_tag.');
		}

		return [
			'previous' => $startingTag,
			'latest' => $endingTag
		];
	}

	private function addToChangeLog($label, $pullRequest) {
		$section = $this->changeLog->getSections()->get($label);

		if (is_null($section)) {
			$section = new Section($this->sectionLabels[$label]);
			$this->changeLog->addSectionByKey($label, $section);
		}

		$entry = new SectionEntry($pullRequest);
		$section->addEntry($entry);
	}

	private function addPullRequest($pullRequest) {
		if ($pullRequest->getBaseBranch() !== 'master') {
			return;
		}

		$labels = [];
		$foundSection = false;
		if (preg_match_all('/\[([a-zA-Z]+)\]/', $pullRequest->getTitle(), $labels)) {
			foreach ($labels[1] as $label) {
				$label = strtolower($label);
				if (array_key_exists($label, $this->sections)) {
					$this->addToChangeLog($label, $pullRequest);
					$foundSection = true;
					break;
				}
			}
		}

		if (!$foundSection) {
			$this->addToChangeLog('misc', $pullRequest);
		}
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->client = new Client();

		$storage = ProjectStorage::getStorage();
		if ($sections = $storage->get('changelog.sections')) {
			foreach ($sections as $section => $description) {
				$this->sections[$section] = [];
				$this->sectionLabels[$section] = $description;
			}
		}

		$output->writeln('<info>Fetching latest tags...</info>');
		$temp = [];
		$this->exec('git fetch -t', $temp);
		$remoteUrl = $this->exec('git config --get remote.origin.url');

		$matches = [];
		if ( !preg_match('/^git@github\.com\:(?P<owner>.+)\/(?P<repo>.+)\.git$/', $remoteUrl[0], $matches) ) {
			throw new RuntimeException('Cannot find a remote git repository.');
		}
		$repoOwner = $matches['owner'];
		$repository = $matches['repo'];
		$this->client->setRepository($repoOwner, $repository);

		$output->writeln('<info>Fetching pull request information...</info>');
		$accessToken = $this->getAccessToken();

		$tags = $this->getReleaseTags($input);
		$command = sprintf(
			'git --no-pager log --pretty=oneline --merges %s...%s | perl -n -e \'/Merge pull request #(\d+)/ && print "$1\n"\'',
			$tags['previous'],
			$tags['latest']
		);
		$pullRequestNumbers = $this->exec($command);

		$this->changeLog = new ChangeLog($tags['previous'], $tags['latest']);

		$response = $this->client->getPullRequests();
		$response = json_decode($response->getBody());
		$pullRequests = [];

		foreach ($pullRequests as $pullRequest) {
			$pullRequest = new PullRequest($pullRequest);
			if (false !== $index = array_search($pullRequest->getNumber(), $pullRequestNumbers)) {
				$this->addPullRequest($pullRequest);
				unset($pullRequestNumbers[$index]);
			}
		}

		foreach ($pullRequestNumbers as $pullRequest) {
			$response = $this->client->getPullRequest($pullRequest);
			$response = new PullRequest(json_decode($response->getBody()));

			$this->addPullRequest($response);
		}

		if ($formatterClass = $storage->get('changelog.formatter'))
		{
			$formatter = new $formatterClass();
			$formatter->format($this->changeLog);
		}

		$output->writeln(sprintf('<info>%s</info>', $this->changeLog->getTitle()));

		foreach ($this->changeLog->getSections() as $section) {
			if ($section->getEntries()->isEmpty()) {
				continue;
			}

			$output->writeln(sprintf('   <comment>%s</comment>', $section->getTitle()));
			foreach ($section->getEntries() as $entry) {
				$output->writeln(sprintf('      %s #%s', $entry->getTitle(), $entry->getPullRequest()->getNumber()));

				foreach ($entry->getSubText() as $subText) {
					$output->writeln(sprintf('         %s', $subText));
				}
			}
		}

		$output->writeln('');

		$output->writeln('<info>The following people failed to label their PRs</info>');
		foreach ($this->changeLog->getSections()->get('misc') as $entry) {
			$pullRequest = $entry->getPullRequest();
			$output->writeln(sprintf('   #%s - %s', $pullRequest->getNumber(), $pullRequest->getAuthor()->getLogin()));

			foreach ($entry->getSubText() as $subText) {
				$output->writeln(sprintf('   %s', $subText));
			}
		}
	}
}
