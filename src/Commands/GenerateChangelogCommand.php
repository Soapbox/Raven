<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use GuzzleHttp\Client;
use SoapBox\Raven\Utils\Command;
use SoapBox\Raven\Utils\RavenStorage;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateChangelogCommand extends Command {
	protected $command = 'generate-changelog';
	protected $description = 'Generate a changelog for the current repo';

	protected function addArguments() {}
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

	private function getAccessToken(Client $client) {
		$storage = RavenStorage::getStorage();

		if (($accessToken = $storage->get('github_access_token')) === '') {
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
		$tags = array_slice($tags, count($tags) - 2);

		return [
			'previous' => $tags[0],
			'latest' => $tags[1]
		];
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$remoteUrl = $this->exec('git config --get remote.origin.url');

		$matches = [];
		if ( !preg_match('/^git@github\.com\:(?P<owner>.+)\/(?P<repo>.+)\.git$/', $remoteUrl[0], $matches) ) {
			throw new RuntimeException('Cannot find a remote git repository.');
		}
		$repoOwner = $matches['owner'];
		$repository = $matches['repo'];

		$client = new Client(['base_uri' => 'https://api.github.com/']);

		$accessToken = $this->getAccessToken($client);

		$tags = $this->getReleaseTags($input);
		$command = sprintf(
			'git --no-pager log --pretty=oneline --merges %s...%s | perl -n -e \'/Merge pull request #(\d+)/ && print "$1\n"\'',
			$tags['previous'],
			$tags['latest']
		);

		$out = [];

		$pullRequests = $this->exec($command);
		foreach ($pullRequests as $pullRequest) {
			$url = sprintf(
				'/repos/%s/%s/pulls/%s',
				$repoOwner,
				$repository,
				$pullRequest
			);

			$response = $client->request('GET', $url, [
			    'query' => ['access_token' => $accessToken]
			]);
			$response = json_decode($response->getBody());

			// $labels = [];
			// if (preg_match_all('/\[([a-zA-Z]+)\]/', '[test][this][some][more]word', $labels)) {
			// 	foreach ($labels as $label) {

			// 	}
			// }

			$out[$pullRequest] = $response->title;
		}

		var_dump($out);
	}
}
