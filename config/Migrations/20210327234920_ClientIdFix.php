<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class ClientIdFix extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change()
	{
		$tablesToChange = [
			'agencies',
			'alert_reasons', // @deprecated
			'appeal_hearings',
			'appeal_letter_items',
			'appeal_levels',
			'appeal_not_defendable_reasons',
			'appeal_notes',
			'appeal_reference_numbers',
			'appeal_template_items',
			'appeal_templates',
			'appeal_types',
			'appeals',
			'blurb_categories',
			'blurb_footnotes',
			'blurb_tags',
			'blurbs',
			'calendar_events',
			'case_activity',
			'case_denial_reasons',
			'case_evidence_criteria',
			'case_outcomes',
			'case_readmissions',
			'case_types',
			'case_umt_answers',
			'client_employee_types',
			'client_employees',
			'company_messages', // @deprecated
			'days_to_respond_froms',
			'denial_reasons',
			'denial_types',
			'evidence_criteria',
			'facility_faxes',
			'facility_types',
			'guidelines',
			'incoming_documents',
			'insurance_provider_agencies',
			'insurance_provider_appeal_levels',
			'insurance_provider_insurance_types',
			'insurance_provider_opportunities',
			'insurance_provider_opportunity_sets',
			'insurance_providers',
			'insurance_types',
			'not_defendable_reasons',
			'outgoing_documents',
			'patients',
			'reference_numbers',
			'roles',
			'roles_permissions',
			'tags',
			'users',
			'users_roles',
			'utilization_management_tools',
			'withdrawn_reasons'
		];

		foreach ($tablesToChange as $tableName) {
			if ($this->table($tableName)->exists()) {
				$this->table($tableName)
					->addColumn('client_id', 'integer', [
						'default' => null,
						'limit' => 10,
						'null' => true,
					])
					->addIndex([
						'client_id'
					])
					->addForeignKey('client_id', 'clients', 'id')
					->update();
			}
		}
	}
}
