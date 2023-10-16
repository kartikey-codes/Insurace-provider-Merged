<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\StorageServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class CheckStorageCommand extends Command
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
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
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
	 * @throws \Cake\I18n\Exception\I18nException
	 * @throws \Cake\Core\Exception\CakeException
	 * @throws \Cake\Console\Exception\StopException
	 */
	public function execute(Arguments $args, ConsoleIo $io): void
	{
		$containers = Configure::readOrFail('Storage.containers');

		$io->out('Checking storage locations exist...');

		// Key = App named storage (i.e. incomingDocuments),
		// Value = Configured folder/container name (i.e. incoming-documents)
		foreach ($containers as $containerAppName => $containerConfiguredName) {
			$io->verbose("Checking if {$containerAppName} exists as: {$containerConfiguredName}...");

			if ($this->storage->exists($containerConfiguredName)) {
				$io->info("{$containerAppName} exists as: {$containerConfiguredName}");
			} else {
				$io->warning("WARNING: {$containerAppName} does not exist as: {$containerConfiguredName}");

				if ($this->storage->create($containerConfiguredName)) {
					$io->info("CREATED: {$containerAppName} as: {$containerConfiguredName}");
				} else {
					$io->error("ERROR: {$containerAppName} unable to be created as: {$containerConfiguredName}");
				}
			}
		}

		$io->success(__('Completed checking storage locations.'));
	}
}
