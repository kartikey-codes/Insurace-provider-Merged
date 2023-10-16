<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Entity\Appeal;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;

class AssignAppealsToVendorsCommand extends Command
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
		$vendors = $this->fetchTable('Vendors');

		$appealsResubmitted = $appeals
			->find('resubmitted', ['skipTenantCheck' => true])
			->all();

		if ($appealsResubmitted->isEmpty()) {
			$io->out(__('No resubmitted appeals found to re-assign to vendors.'));
		} else {
			foreach ($appealsResubmitted as $appeal) {
				$appeal->set('appeal_status', Appeal::STATUS_ASSIGNED);

				$io->out(__(
					'Reassigning submitted appeal #{0} to vendor.',
					$appeal->id
				));

				$appeals->saveOrFail($appeal, [
					'skipTenantCheck' => true,
				]);
			}
		}

		$appeals = $appeals
			->find('assignableToVendor', ['skipTenantCheck' => true])
			->all();

		if ($appeals->isEmpty()) {
			$io->out(__('No submitted appeals found to assign to vendors.'));

			return;
		}

		$count = $appeals->count();
		$io->out(__('Found {0} submitted appeals to assign to vendors.', $count));

		foreach ($appeals as $appeal) {
			$vendor = $vendors->find('assignableToAppeal', [
				'appeal' => $appeal,
				'skipTenantCheck' => true,
			])->first();

			$io->out(__(
				'Found vendor {0} (#{1}) as eligible for appeal #{2}',
				$vendor->name,
				$vendor->id,
				$appeal->id
			));

			$appeal->assignToVendor($vendor);

			$appeals->saveOrFail($appeal, [
				'skipTenantCheck' => true,
			]);

			$event = new Event('Model.Appeal.assignedToVendor', $this, [$appeal]);
			$appeals->getEventManager()->dispatch($event);

			$io->success(__(
				'Assigned appeal #{0} to {1} (#{2})',
				$appeal->id,
				$vendor->name,
				$vendor->id
			));
		}

		$io->out('All done!');
	}
}
