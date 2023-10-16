<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserAddCommand extends Command
{
	use LocatorAwareTrait;

	/**
	 * Build Options
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 * @throws \App\Command\LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		$parser->addArgument('email', [
			'help' => 'The email address for the new user.',
			'required' => true,
		]);

		$parser->addArgument('password', [
			'help' => 'A password for the new user. Will be hashed.',
			'required' => true,
		]);

		$parser->addArgument('first_name', [
			'help' => 'User\'s first/given name.',
			'required' => true,
		]);

		$parser->addArgument('last_name', [
			'help' => 'User\'s last/family name.',
			'required' => true,
		]);

		return $parser;
	}

	/**
	 * Execute
	 *
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return void
	 * @throws \Cake\Console\Exception\StopException
	 * @throws \Cake\Database\Exception\DatabaseException
	 * @throws \RuntimeException
	 * @throws \Cake\ORM\Exception\MissingEntityException
	 * @throws \InvalidArgumentException
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$users = $this->fetchTable('Users');

		try {
			$user = $users->newEntity([
				'email' => $args->getArgument('email'),
				'password' => $args->getArgument('password'),
				'first_name' => $args->getArgument('first_name'),
				'last_name' => $args->getArgument('last_name'),
				'active' => true,
			]);

			$users->saveOrFail($user, [
				'skipTenantCheck' => true,
			]);

			$io->success(__(
				'User ({0}) has been added with ID #{1}',
				$user->email,
				$user->id
			));
		} catch (PersistenceFailedException $e) {
			$io->abort(__(
				'Unable to create user. The email address may be in use or an invalid value was provided. Details: {0}',
				$e->getMessage()
			));
		}
	}
}
