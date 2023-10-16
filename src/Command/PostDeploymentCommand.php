<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\StorageServiceInterface;
use Cake\Command\Command;
use Cake\Command\SchemacacheClearCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Handles running various other command shells when deploying
 * the app based on the environment.
 *
 * Seems to require dependencies injected here to be passed down.
 *
 * @package App\Command
 */
class PostDeploymentCommand extends Command
{
	/**
     * @var \App\Service\StorageServiceInterface
     */
	private StorageServiceInterface $storage;

	/**
	 * Constructor
	 *
	 * @param \App\Service\StorageServiceInterface $storage
	 * @return void
	 * @throws \App\Command\InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \App\Command\UnexpectedValueException
	 */
	public function __construct(StorageServiceInterface $storage)
	{
		parent::__construct();
		$this->storage = $storage;
	}

	/**
	 * Build Options
	 *
	 * @param \Cake\Console\ConsoleOptionParser $parser
	 * @return \Cake\Console\ConsoleOptionParser
	 * @throws \App\Command\LogicException
	 */
	protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
	{
		return $parser;
	}

	/**
	 * Execute
	 *
	 * @param \Cake\Console\Arguments $args
	 * @param \Cake\Console\ConsoleIo $io
	 * @return void
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \App\Command\UnexpectedValueException
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Console\Exception\StopException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		// Migrations
		$this->executeCommand(RunAllMigrationsCommand::class, [], $io);

		// Storage
		$checkStorage = new CheckStorageCommand($this->storage);
		$this->executeCommand($checkStorage, [], $io);

		// Cache
		$this->executeCommand(SchemacacheClearCommand::class, [], $io);
		$this->executeCommand(ClearAllCachesCommand::class, [], $io);
	}
}
