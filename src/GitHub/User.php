<?php namespace SoapBox\Raven\GitHub;

use Raven\Api\GitHub\User as UserInterface;

class User implements UserInterface {
	private $userResponse;

	public function __construct($userResponse) {
		$this->userResponse = $userResponse;
	}

	/**
	 * Get the user's login username
	 *
	 * @return string
	 */
	public function getLogin() {
		return $this->userResponse->login;
	}
}
