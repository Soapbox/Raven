<?php namespace SoapBox\Raven\Commands;

use InvalidArgumentException;
use RuntimeException;
use GuzzleHttp\Client;
use SoapBox\Raven\ChangeLog\ChangeLog;
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

		if ('' === $accessToken = $storage->get('github_access_token')) {
			$client = new Client(['base_uri' => 'https://api.github.com/']);

			$email = $this->exec('git config --global user.email')[0];

			$question = new Question(sprintf('Enter host password for user \'%s\':', $email));
			$question->setHidden(true);
			$question->setHiddenFallback(false);

			$helper = $this->getHelper('question');
			$password = $helper->ask($input, $output, $question);

			$params = [
				"scopes" => [
					"repo"
				],
				"note" => "Raven"
			];

			$response = $client->post('/authorizations', [
				'auth' => [$email, $password],
				'body' => json_encode($params)
			]);

			$accessToken = json_decode($response->getBody())->token;
			$storage->set('github_access_token', $accessToken);
		}

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
					$this->sections[$label][] = $pullRequest;
					$foundSection = true;
					break;
				}
			}
		}

		if (!$foundSection) {
			$this->sections['misc'][] = $pullRequest;
		}
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$storage = ProjectStorage::getStorage();
		$sections = $storage->get('changelog.sections');

		foreach ($sections as $section => $description) {
			$this->sections[$section] = [];
			$this->sectionLabels[$section] = $description;
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

		$output->writeln('<info>Fetching pull request information...</info>');
		$accessToken = $this->getAccessToken();

		$tags = $this->getReleaseTags($input);
		$command = sprintf(
			'git --no-pager log --pretty=oneline --merges %s...%s | perl -n -e \'/Merge pull request #(\d+)/ && print "$1\n"\'',
			$tags['previous'],
			$tags['latest']
		);
		$pullRequestNumbers = $this->exec($command);

		$url = sprintf(
			'https://api.github.com/repos/%s/%s/pulls',
			$repoOwner,
			$repository
		);
		$client = new Client(['base_uri' => $url]);

		$response = $client->request('GET', null, [
			'query' => [
				'access_token' => $accessToken,
				'state' => 'closed',
				'sort' => 'updated',
				'direction' => 'desc',
				'per_page' => 50
			]
		]);
		$response = json_decode($response->getBody());
		$pullRequests = [];

		foreach ($response as $prContent) {
			$pullRequests[] = new PullRequest($prContent);
		}

		foreach ($pullRequests as $pullRequest) {
			if (false !== $index = array_search($pullRequest->getNumber(), $pullRequestNumbers)) {
				$this->addPullRequest($pullRequest);
				unset($pullRequestNumbers[$index]);
			}
		}

		foreach ($pullRequestNumbers as $pullRequest) {
			$path = sprintf('pulls/%s', $pullRequest);
			$response = $client->request('GET', $path, [
				'query' => ['access_token' => $accessToken]
			]);
			$response = new PullRequest(json_decode($response->getBody()));

			$this->addPullRequest($response);
		}

		$changeLog = new ChangeLog();

		$output->writeln(sprintf('<info>Changes from %s to %s</info>', $tags['previous'], $tags['latest']));
		foreach ($this->sections as $section => $pullRequests) {
			if (count($pullRequests) === 0) {
				continue;
			}

			$output->writeln(sprintf('  <comment>%s</comment>', $this->sectionLabels[$section]));
			foreach ($pullRequests as $pullRequest) {
				$title = trim(preg_replace('/^\[.*\]/', '', $pullRequest->getTitle()));
				$output->writeln(sprintf('      %s #%s', $title, $pullRequest->getNumber()));
			}
			$output->writeln('');
		}

		$output->writeln('<info>The following people failed to label their PRs</info>');
		foreach ($this->sections['misc'] as $pullRequest) {
			$output->writeln(sprintf('  #%s - %s', $pullRequest->getNumber(), $pullRequest->getAuthor()->getLogin()));
		}
	}
}
