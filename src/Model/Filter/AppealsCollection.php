<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Entity\Appeal;
use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class AppealsCollection extends FilterCollection
{
	/**
	 * Initialize search filters
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		// Map 'search' to multiple potential filters
		$this->add('search', AliasedFilters::class, [
			'filters' => [
				'patient_name',
			],
		]);

		$this->add('status', 'Search.Callback', [
			'callback' => function ($query, $args, $manager) {
				if (isset($args['status'])) {
					switch ($args['status']) {
						case Appeal::STATUS_OPEN:
							$query->find('open', $args);
							break;
						case Appeal::STATUS_UTC:
							$query->find('utc', $args);
							break;
						case Appeal::STATUS_CANCELLED:
							$query->find('cancelled', $args);
							break;
						case Appeal::STATUS_CLOSED:
							$query->find('closed', $args);
							break;
						default:
							$query->find('byStatus', $args);
							break;
					}
				}

				return true;
			},
			'filterEmpty' => true,
		]);

		$this->add('case_id', 'Search.Value', [
			'fields' => 'case_id',
			'filterEmpty' => true,
		]);

		$this->add('agency_id', 'Search.Value', [
			'fields' => 'agency_id',
			'filterEmpty' => true,
		]);

		$this->add('appeal_type_id', 'App.NullableId', [
			'fields' => 'appeal_type_id',
			'filterEmpty' => true,
		]);

		$this->add('appeal_level_id', 'Search.Value', [
			'fields' => 'appeal_level_id',
			'filterEmpty' => true,
		]);

		$this->add('defendable', 'App.Boolean', [
			'fields' => 'defendable',
			'filterEmpty' => true,
		]);

		$this->add('letter_date', 'Search.Value', [
			'fields' => 'letter_date',
			'filterEmpty' => true,
		]);

		$this->add('letter_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'letter_date',
			'filterEmpty' => true,
		]);

		$this->add('letter_date_end', 'App.EndDate', [
			'fields' => 'letter_date',
			'filterEmpty' => true,
		]);

		$this->add('received_date', 'Search.Value', [
			'fields' => 'received_date',
			'filterEmpty' => true,
		]);

		$this->add('received_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'received_date',
			'filterEmpty' => true,
		]);

		$this->add('received_date_end', 'App.EndDate', [
			'fields' => 'received_date',
			'filterEmpty' => true,
		]);

		$this->add('due_date', 'Search.Value', [
			'fields' => 'due_date',
			'filterEmpty' => true,
		]);

		$this->add('due_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'due_date',
			'filterEmpty' => true,
		]);

		$this->add('due_date_end', 'App.EndDate', [
			'fields' => 'due_date',
			'filterEmpty' => true,
		]);

		$this->add('hearing_date', 'Search.Value', [
			'fields' => 'hearing_date',
			'filterEmpty' => true,
		]);

		$this->add('hearing_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'hearing_date',
			'filterEmpty' => true,
		]);

		$this->add('hearing_date_end', 'App.EndDate', [
			'fields' => 'hearing_date',
			'filterEmpty' => true,
		]);

		$this->add('priority', 'App.Boolean', [
			'fields' => 'priority',
			'filterEmpty' => true,
		]);

		$this->add('assigned_to', 'App.NullableId', [
			'fields' => 'assigned_to',
			'filterEmpty' => true,
		]);

		$this->add('completed_by', 'App.NullableId', [
			'fields' => 'completed_by',
			'filterEmpty' => true,
		]);

		$this->add('audit_reviewer_id', 'App.NullableId', [
			'fields' => 'audit_reviewer_id',
			'filterEmpty' => true,
		]);

		$this->add('audit_identifier', 'Search.Value', [
			'fields' => 'audit_identifier',
			'filterEmpty' => true,
		]);

		$this->add('created_by', 'App.NullableId', [
			'fields' => 'created_by',
			'filterEmpty' => true,
		]);

		$this->add('modified_by', 'App.NullableId', [
			'fields' => 'modified_by',
			'filterEmpty' => true,
		]);

		/**
		 * Case Filters
		 */

		$this->add('facility_id', 'App.NullableId', [
			'fields' => 'Cases.facility_id',
			'filterEmpty' => true,
		]);

		$this->add('patient_id', 'App.NullableId', [
			'fields' => 'Cases.patient_id',
			'filterEmpty' => true,
		]);

		$this->add('case_type_id', 'App.NullableId', [
			'fields' => 'Cases.case_type_id',
			'filterEmpty' => true,
		]);

		$this->add('denial_type_id', 'App.NullableId', [
			'fields' => 'Cases.denial_type_id',
			'filterEmpty' => true,
		]);

		$this->add('case_outcome_id', 'App.NullableId', [
			'fields' => 'Cases.case_outcome_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_provider_id', 'App.NullableId', [
			'fields' => 'Cases.insurance_provider_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_type_id', 'App.NullableId', [
			'fields' => 'Cases.insurance_type_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_plan', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'Cases.insurance_plan',
			'filterEmpty' => true,
		]);

		$this->add('insurance_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'Cases.insurance_number',
			'filterEmpty' => true,
		]);

		$this->add('visit_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'Cases.visit_number',
			'filterEmpty' => true,
		]);

		/**
		 * Patient Filters
		 */

		$this->add('patient_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'Patients.first_name',
			'middleName' => 'Patients.middle_name',
			'lastName' => 'Patients.last_name',
			'searchFirstLast' => true,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => true,
			'searchLastFirstMiddle' => true,
		]);

		$this->add('patient_list_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'Patients.first_name',
			'middleName' => 'Patients.middle_name',
			'lastName' => 'Patients.last_name',
			'searchFirstLast' => false,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => true,
		]);
	}
}
