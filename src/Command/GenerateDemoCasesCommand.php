<?php

declare(strict_types=1);

namespace App\Command;

use App\Factory\FactoryInterface;
use App\Factory\CaseFactory;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;

class GenerateDemoCasesCommand extends Command
{
	/**
	 * How many records to fake
	 * @var int
	 */
	public int $count = 10;

	/**
	 * Alias of the table we're using for factory records
	 * @var string
	 */
	public string $tableName = 'Cases';

	/**
	 * The factory class this command uses
	 * @var FactoryInterface
	 */
	private FactoryInterface $factory;

	/**
	 * Initialize the command
	 * @return void
	 */
	public function initialize(): void
	{
		$this->factory = new CaseFactory();
	}

	/**
	 * Build Options
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 * @throws \LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		$parser->addArgument('client_id', [
			'help' => 'The client ID to generate demo data for.',
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
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Console\Exception\StopException
	 * @throws \UnexpectedValueException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$clientId = $args->getArgument('client_id');
		if (empty($clientId)) {
			throw new RecordNotFoundException(__('No client ID was provided.'));
		}

		try {
			$clients = $this->fetchTable('Clients')->removeBehavior('Multitenancy');
			$client = $clients->get($clientId, ['skipTenantCheck' => true]);
		} catch (RecordNotFoundException $e) {
			$io->abort(__('Client #"{0}" could not be found.', $clientId));
			return;
		}

		$io->out(__(
			'Using Client #{0} {1} for demo data.',
			$client->id,
			$client->name,
		));

		$table = $this->fetchTable($this->tableName);

		if ($table->hasBehavior('Multitenancy')) {
			$table = $table->removeBehavior('Multitenancy');
		}

		if ($table->hasBehavior('Vendor')) {
			$table = $table->removeBehavior('Vendor');
		}

		$this->factory->setClient($client);

		for ($i = 0; $i < $this->count; $i++) {
			$entity = $this->factory->fake();
			$entity->set('client_id', $client->id);

			$table->saveOrFail($entity, [
				'skipTenantCheck' => true,
				'skipVendorCheck' => true
			]);
		}

		$io->success(__(
			'Created {0} fake records in {1} for client {2}.',
			$this->count,
			$this->tableName,
			$client->name
		));
	}
}
