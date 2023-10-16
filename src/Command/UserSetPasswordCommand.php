<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserSetPasswordCommand extends Command
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
			'help' => 'The email address for the user.',
			'required' => true,
		]);

		$parser->addArgument('password', [
			'help' => 'A new password for the user. Will be hashed.',
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
	 * @throws \App\Command\MissingModelException
	 * @throws \App\Command\UnexpectedValueException
	 * @throws \App\Command\I18nException
	 * @throws \App\Command\CakeException
	 * @throws \App\Command\StopException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$users = $this->fetchTable('Users');
		$email = $args->getArgument('email');
		$password = $args->getArgument('password');

		if (empty($email) || empty($password)) {
			$io->abort(__('An email address and password must be provided as arguments.'));
		}

		try {
			$user = $users
				->findByEmail($email)
				->firstOrFail();

			$user->set('password', $password);

			$users->saveOrFail($user, [
				'skipTenantCheck' => true,
			]);

			$io->out(__(
				'User #{0} ({1}) has had their password reset.',
				$user->id,
				$user->full_name
			));
		} catch (RecordNotFoundException $e) {
			$io->abort(__(
				'The user "{0}" could not be found.',
				$email
			));
		}
	}
}
