<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;

class ClearAssignedAppealsToVendorCommand extends Command
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
		$parser->addArgument('id', [
			'help' => 'The ID for the vendor.',
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
		$appeals = $this->fetchTable('Appeals');
		$vendorId = $args->getArgument('id');
		$vendors = $this->fetchTable('Vendors');

		$vendor = $vendors->get($vendorId);

		$io->out(__('Vendor: {0}', $vendor->name));

		$appeals = $appeals
			->find('assignedToVendor', ['skipTenantCheck' => true])
			->find('assigned')
			->find('assignedToVendorById', [
				'id' => $vendorId,
			])
			->all();

		if ($appeals->isEmpty()) {
			$io->out(__('No assigned appeals found for this vendor.'));

			return;
		}

		$count = $appeals->count();

		if ($io->ask(__('Clear {0} appeal(s) from vendor {1}?', $count, $vendor->name), 'n', ['y', 'n']) === 'n') {
			$io->error(__('Skipped clearing queue.'));
			$this->abort();
		}

		foreach ($appeals as $appeal) {
			$appeal->revertToSubmitted();

			$appeals->saveOrFail($appeal, [
				'skipTenantCheck' => true,
			]);

			$io->success(__('Unassigned appeal #{0}', $appeal->id));
		}

		$io->out('All done.');
	}
}
