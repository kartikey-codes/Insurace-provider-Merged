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

class ClientSubscriptionResetCommand extends Command
{
	use LocatorAwareTrait;

	/**
	 * Build Options
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 * @throws \LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		$parser->addArgument('email', [
			'help' => 'The email address for the client.',
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
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Console\Exception\StopException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$clients = $this->fetchTable('Clients');
		$email = $args->getArgument('email');

		if (empty($email)) {
			$io->abort(__('An email address must be provided as an argument.'));
		}

		try {
			$options = [
				'skipTenantCheck' => true,
			];

			$client = $clients
				->find('all', $options)
				->where([
					'email' => $email,
				])
				->firstOrFail();

			$clients->clearSubscription($client, $options);

			$io->out(__(
				'Client #{0} ({1}) with email ({2}) has had their subscription data reset.',
				$client->id,
				$client->name,
				$client->email
			));
		} catch (RecordNotFoundException $e) {
			$io->abort(__(
				'A client with email address "{0}" could not be found.',
				$email
			));
		} catch (PersistenceFailedException $e) {
			$io->abort(__(
				'The client with email "{0}" failed to reset.',
				$email
			));
		}
	}
}
