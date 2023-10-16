<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserDeactivateCommand extends Command
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

		return $parser;
	}

	/**
	 * Execute
	 *
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return void
	 * @throws \App\Command\StopException
	 * @throws \App\Command\DatabaseException
	 * @throws \App\Command\RuntimeException
	 * @throws \App\Command\MissingEntityException
	 * @throws \App\Command\InvalidArgumentException
	 * @throws \App\Command\I18nException
	 * @throws \App\Command\CakeException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$users = $this->fetchTable('Users');

		$email = $args->getArgument('email');

		if (empty($email)) {
			$io->abort(__('An email address must be provided as an argument.'));
		}

		try {
			$user = $users
				->findByEmail($email)
				->firstOrFail();

			$user->set('active', false);

			$users->saveOrFail($user, [
				'skipTenantCheck' => true,
			]);

			$io->out(__(
				'User #{0} ({1}) has been deactivated.',
				$user->id,
				$user->full_name
			));
		} catch (RecordNotFoundException $e) {
			$io->abort(__(
				'The user "{0}" could not be found.',
				$email
			));
		} catch (PersistenceFailedException $e) {
			$io->abort(__(
				'The user "{0}" failed to deactivate.',
				$email
			));
		}
	}
}
