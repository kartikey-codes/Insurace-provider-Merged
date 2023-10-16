<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Model\Entity\Appeal;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Dashboard Controller
 */
class DashboardController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * This controller does not have an inflected model
	 *
	 * @var string
	 */
	public $modelClass = false;

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
	}

	/**
	 * Before filter callback.
	 * Executes before every request.
	 *
	 * @param \Cake\Event\Event $event The beforeFilter event.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void
	{
		parent::beforeFilter($event);
	}

	/**
	 * Index method
	 *
	 * Returns all data used on the dashboard page
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->set([
			'returned_appeals' => $this->getReturnedAppeals(),
			'completed_appeals' => $this->getCompletedAppeals(),
			'assigned_documents' => $this->countAssignedDocuments(),
			'assigned_cases' => $this->countAssignedCases(),
			'assigned_appeals' => $this->countAssignedAppeals(),
		]);
	}

	/**
	 * Count incoming documents assigned to current user
	 *
	 * @return int
	 */
	private function countAssignedDocuments(): int
	{
		return $this->fetchTable('IncomingDocuments')
			->find('new')
			->find('assignedTo', [
				'UserId' => $this->currentUser->id,
			])
			->count();
	}

	/**
	 * Count assigned cases
	 *
	 * @return int
	 */
	private function countAssignedCases(): int
	{
		return $this->fetchTable('Cases')
			->find('open')
			->find('assignedTo', [
				'UserId' => $this->currentUser->id,
			])
			->count();
	}

	/**
	 * Count assigned appeals
	 *
	 * @return int
	 */
	private function countAssignedAppeals(): int
	{
		return $this->fetchTable('Appeals')
			->find('notCompleted')
			->find('assignedTo', [
				'UserId' => $this->currentUser->id,
			])
			->count();
	}

	/**
	 * Get returned appeals
	 *
	 * @return array
	 */
	private function getReturnedAppeals(): array
	{
		$query = $this->fetchTable('Appeals')
			->find('byStatus', [
				'status' => Appeal::STATUS_RETURNED,
				'order' => [
					'due_date' => 'asc',
				],
			]);

		return $query
			->contain([
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			])
			->select([
				'id',
				'case_id',
				'appeal_status',
				'patient' => $query->func()->concat([
					'Patients.last_name' => 'identifier',
					', ',
					'Patients.first_name' => 'identifier',
				]),
				'appeal_level' => 'AppealLevels.name',
				'due_date',
			])
			->limit(100)
			->disableHydration()
			->toArray();
	}

	/**
	 * Get completed appeals
	 *
	 * @return array
	 */
	private function getCompletedAppeals(): array
	{
		$query = $this->fetchTable('Appeals')
			->find('byStatus', [
				'status' => Appeal::STATUS_COMPLETED,
				'order' => [
					'due_date' => 'asc',
				],
			]);

		return $query
			->contain([
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			])
			->select([
				'id',
				'case_id',
				'appeal_status',
				'patient' => $query->func()->concat([
					'Patients.last_name' => 'identifier',
					', ',
					'Patients.first_name' => 'identifier',
				]),
				'appeal_level' => 'AppealLevels.name',
				'due_date',
			])
			->limit(100)
			->disableHydration()
			->toArray();
	}

	/**
	 * Recent appeal notes method
	 *
	 * @return void
	 */
	public function recentNotes(): void
	{
		$appealNotes = $this->fetchTable('AppealNotes')
			->find('search', [
				'search' => $this->getRequest()->getQuery(),
			])
			->contain([
				'Appeals',
				'CreatedByUser'
			])
			->matching('Appeals')
			->orderDesc('AppealNotes.created');

		$this->set('data', $this->paginate($appealNotes, [
			'limit' => 5
		]));
		$this->set('pagination', $this->getRequest()->getAttribute('paging')['AppealNotes']);
	}
}
