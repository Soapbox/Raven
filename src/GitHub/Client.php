<?php namespace SoapBox\Raven\GitHub;

use RuntimeException;
use GuzzleHttp\Client as GuzzleClient;

class Client {
	private $accessToken;
	private $repository;

	public function __construct($accessToken = null)
	{
		$this->setAccessToken($accessToken);
		$this->client = new GuzzleClient(['base_uri' => 'https://api.github.com/']);
	}

	private function getAccessToken()
	{
		return $this->accessToken;
	}

	private function getRepository()
	{
		if (empty($this->repository)) {
			throw new RuntimeException('You must specify a repository before fetching pull requests.');
		}

		return $this->repository;
	}

	private function getPullRequestPath($number = null)
	{
		$path = sprintf('/repos/%s/pulls', $this->getRepository());

		if (!is_null($number)) {
			$path = sprintf('%s/%s', $path, $number);
		}

		return $path;
	}

	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
	}

	public function setRepository($owner, $repository)
	{
		$this->repository = sprintf('%s/%s', $owner, $repository);
	}

	public function getPullRequests()
	{
		return $this->client->get($this->getPullRequestPath(), [
			'query' => [
				'access_token' => $this->getAccessToken(),
				'state' => 'closed',
				'sort' => 'updated',
				'direction' => 'desc',
				'per_page' => 50
			]
		]);
	}

	public function getPullRequest($number)
	{
		return $this->client->get($this->getPullRequestPath($number), [
			'query' => [
				'access_token' => $this->getAccessToken()
			]
		]);
	}

	public function acquireAccessToken($login, $password)
	{
		$params = [
			"scopes" => [
				"repo"
			],
			"note" => "Raven"
		];

		$response = $this->client->post('/authorizations', [
			'auth' => [$email, $password],
			'body' => json_encode($params)
		]);

		$accessToken = json_decode($response->getBody())->token;
		return $accessToken;
	}
}
