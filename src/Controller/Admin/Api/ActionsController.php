<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Model\Entity\Appeal;
use App\Model\Table\AppealsTable;
use App\Model\Table\VendorsTable;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Actions Controller
 */
class ActionsController extends ApiController
{
	use LocatorAwareTrait;

	/**
     * @var \App\Model\Table\AppealsTable
     */
	public AppealsTable $Appeals;

	/**
     * @var \App\Model\Table\VendorsTable
     */
	public VendorsTable $Vendors;

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->Appeals = $this->fetchTable('Appeals');
		$this->Vendors = $this->fetchTable('Vendors');
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function assignAppeals(): void
	{
		$this->request->allowMethod(['get']);

		$appealsResubmitted = $this->Appeals
			->find('resubmitted', ['skipTenantCheck' => true])
			->all();

		if (!$appealsResubmitted->isEmpty()) {
			foreach ($appealsResubmitted as $appeal) {
				$appeal->set('appeal_status', Appeal::STATUS_ASSIGNED);

				$this->Appeals->saveOrFail($appeal, [
					'skipTenantCheck' => true,
				]);
			}
		}

		$appeals = $this->Appeals
			->find('assignableToVendor', ['skipTenantCheck' => true])
			->all();

		$submitted = $appeals->count();
		$assigned = 0;

		if (!$appeals->isEmpty()) {
			foreach ($appeals as $appeal) {
				$vendor = $this->Vendors->find('assignableToAppeal', [
					'appeal' => $appeal,
					'skipTenantCheck' => true,
				])->first();

				$appeal->assignToVendor($vendor);

				$this->Appeals->saveOrFail($appeal, [
					'skipTenantCheck' => true,
				]);

				$event = new Event('Model.Appeal.assignedToVendor', $this, [$appeal]);
				$this->Appeals->getEventManager()->dispatch($event);

				$assigned++;
			}
		}

		$data = [
			'submitted' => $submitted,
			'assigned' => $assigned,
		];

		$this->set(compact('data'));
	}
}
