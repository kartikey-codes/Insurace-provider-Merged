<?php

declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Permissions seed.
 */
class PermissionsSeed extends AbstractSeed
{
	/**
	 * Run Method
	 * @return void
	 */
	public function run(): void
	{
		$this->table('permissions')
			->insert([
				// Cases
				[
					'controller' => 'Cases',
					'action' => 'add',
					'name' => 'Add Cases',
					'category' => 'Cases',
					'key' => 'cases:create'
				],
				[
					'controller' => 'Cases',
					'action' => 'delete',
					'name' => 'Delete Cases',
					'category' => 'Cases',
					'key' => 'cases:delete',
				],
				[
					'controller' => 'Cases',
					'action' => 'close',
					'name' => 'Close Cases',
					'category' => 'Cases',
					'key' => 'cases:close',
				],
				[
					'controller' => 'Cases',
					'action' => 'open',
					'name' => 'Reopen Cases',
					'category' => 'Cases',
					'key' => 'cases:open',
				],
				[
					'controller' => 'CaseFiles',
					'action' => 'add',
					'name' => 'Upload Case Files',
					'category' => 'Cases',
					'key' => 'caseFiles:create',
				],
				// Appeals
				[
					'controller' => 'Appeals',
					'action' => 'add',
					'name' => 'Create Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:create',
				],
				[
					'controller' => 'Appeals',
					'action' => 'assign',
					'name' => 'Assign Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:assign',
				],
				[
					'controller' => 'Appeals',
					'action' => 'submit',
					'name' => 'Submit Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:submit',
				],
				[
					'controller' => 'Appeals',
					'action' => 'cancel',
					'name' => 'Cancel Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:cancel',
				],
				[
					'controller' => 'Appeals',
					'action' => 'reopen',
					'name' => 'Reopen Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:reopen',
				],
				[
					'controller' => 'Appeals',
					'action' => 'complete',
					'name' => 'Complete Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:complete',
				],
				[
					'controller' => 'Appeals',
					'action' => 'delete',
					'name' => 'Delete Appeals',
					'category' => 'Appeals',
					'key' => 'appeals:delete',
				],
				// Requests
				[
					'controller' => 'CaseRequests',
					'action' => 'add',
					'name' => 'Create Requests',
					'category' => 'Requests',
					'key' => 'caseRequests:create',
				],
				// Patients
				[
					'controller' => 'Patients',
					'action' => 'add',
					'name' => 'Create Patients',
					'category' => 'Patients',
					'key' => 'patients:create',
				]
			])
			->save();
	}
}
