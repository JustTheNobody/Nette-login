<?php

declare(strict_types=1);

namespace App\Models;

use Nette\Security\Passwords;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\AuthenticationException;

class MyAuthenticator implements Authenticator
{
	private UsersRepository $users;

	public function __construct(
		private AppModel $orm,
		private Passwords $passwords,
	) {
		$this->users = $this->orm->getRepository( UsersRepository::class );
	}

	public function authenticate(string $email, string $password): SimpleIdentity
	{
		$row = $this->users->findBy(['email' => $email])->fetch();

		if (!$row) {
			throw new AuthenticationException('User not found.');
		}

		if (!$this->passwords->verify($password, $row->password)) {
			throw new AuthenticationException('Invalid password.');
		}

		return new SimpleIdentity(
			$row->id,
			[],
			['username' => $row->email]
		);
	}
}