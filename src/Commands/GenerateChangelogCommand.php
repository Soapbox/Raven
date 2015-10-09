<?php namespace SoapBox\Raven\Commands;

use RuntimeException;
use GuzzleHttp\Client;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output = $this->exec('git config --get remote.origin.url');

		$matches = [];
		if ( !preg_match('/^git@github\.com\:(?P<owner>.+)\/(?P<repo>.+)\.git$/', $output[0], $matches) ) {
			throw new RuntimeException('Cannot find a remote git repository.');
		}

		$tags = $this->exec('git tag');
		natsort($tags);
		$tags = array_slice($tags, count($tags) - 2);

		$previous = $tags[0];
		$latest = $tags[1];

		$command = sprintf(
			'git --no-pager log --pretty=oneline --merges %s...%s | perl -n -e \'/Merge pull request #(\d+)/ && print "$1\n"\'',
			$previous,
			$latest
		);

		$out = [];

		$client = new Client(['base_uri' => 'https://api.github.com/']);
		$pullRequests = $this->exec($command);
		foreach ($pullRequests as $pullRequest) {
			$url = sprintf(
				'/repos/%s/%s/pulls/%s',
				$matches['owner'],
				$matches['repo'],
				$pullRequest
			);

			$response = $client->request('GET', $url, [
			    'query' => ['access_token' => '65e85af1e06a8622374ca26a88415c96b56b945d']
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
		die();
	}
}
