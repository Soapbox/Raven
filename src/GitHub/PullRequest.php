<?php namespace SoapBox\Raven\GitHub;

use Raven\Api\GitHub\PullRequest as PullRequestInterface;

class PullRequest implements PullRequestInterface {
	private $pullRequestResponse;

	public function __construct($pullRequestResponse) {
		$this->pullRequestResponse = $pullRequestResponse;
	}

	/**
	 * Retrieve the body content of a pull request
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->pullRequestResponse->body;
	}

	/**
	 * Retrieve the title of a pull request
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->pullRequestResponse->title;
	}

	/**
	 * Retrieve the pull request number of a pull request
	 *
	 * @return int
	 */
	public function getNumber() {
		return $this->pullRequestResponse->number;
	}

	/**
	 * Get the author of this PullRequest
	 *
	 * @return Raven\Api\GitHub\User
	 */
	public function getAuthor() {
		return new User($this->pullRequestResponse->user);
	}

	/**
	 * Get the name of the base branch for this PullRequest
	 *
	 * @return string
	 */
	public function getBaseBranch() {
		return $this->pullRequestResponse->base->ref;
	}
}
