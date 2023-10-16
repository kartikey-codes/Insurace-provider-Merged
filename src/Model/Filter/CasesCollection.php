<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Entity\CaseEntity;
use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class CasesCollection extends FilterCollection
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

		$this->add('case_type_id', 'App.NullableId', [
			'fields' => 'case_type_id',
			'filterEmpty' => true,
		]);

		$this->add('client_id', 'App.NullableId', [
			'fields' => 'client_id',
			'filterEmpty' => true,
		]);

		$this->add('patient_id', 'App.NullableId', [
			'fields' => 'patient_id',
			'filterEmpty' => true,
		]);

		$this->add('client_employee_id', 'App.NullableId', [
			'fields' => 'client_employee_id',
			'filterEmpty' => true,
		]);

		$this->add('patient_list_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => 'middle_name',
			'lastName' => 'last_name',
			'searchFirstLast' => false,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => true,
		]);

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

		$this->add('facility_id', 'App.NullableId', [
			'fields' => 'facility_id',
			'filterEmpty' => true,
		]);

		$this->add('denial_type_id', 'App.NullableId', [
			'fields' => 'denial_type_id',
			'filterEmpty' => true,
		]);

		$this->add('denial_reason_id', 'Search.Finder', [
			'finder' => 'byDenialReason',
			'filterEmpty' => true,
		]);

		$this->add('case_outcome_id', 'App.NullableId', [
			'fields' => 'case_outcome_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_provider_id', 'App.NullableId', [
			'fields' => 'insurance_provider_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_type_id', 'App.NullableId', [
			'fields' => 'insurance_type_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_plan', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'insurance_plan',
			'filterEmpty' => true,
		]);

		$this->add('disputed_amount_min', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'disputed_amount',
			'filterEmpty' => true,
		]);

		$this->add('disputed_amount_max', 'Search.Compare', [
			'operator' => '<=',
			'fields' => 'disputed_amount',
			'filterEmpty' => true,
		]);

		$this->add('settled_amount_min', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'settled_amount',
			'filterEmpty' => true,
		]);

		$this->add('settled_amount_max', 'Search.Compare', [
			'operator' => '<=',
			'fields' => 'settled_amount',
			'filterEmpty' => true,
		]);

		$this->add('insurance_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'insurance_number',
			'filterEmpty' => true,
		]);

		$this->add('visit_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'visit_number',
			'filterEmpty' => true,
		]);

		$this->add('admit_date', 'Search.Value', [
			'fields' => 'admit_date',
			'filterEmpty' => true,
		]);

		$this->add('admit_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'admit_date',
			'filterEmpty' => true,
		]);

		$this->add('admit_date_end', 'App.EndDate', [
			'fields' => 'admit_date',
			'filterEmpty' => true,
		]);

		$this->add('discharge_date', 'Search.Value', [
			'fields' => 'discharge_date',
			'filterEmpty' => true,
		]);

		$this->add('discharge_date_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'discharge_date',
			'filterEmpty' => true,
		]);

		$this->add('discharge_date_end', 'App.EndDate', [
			'fields' => 'discharge_date',
			'filterEmpty' => true,
		]);

		$this->add('assigned_to', 'App.NullableId', [
			'fields' => 'assigned_to',
			'filterEmpty' => true,
		]);

		$this->add('closed_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'closed',
			'filterEmpty' => true,
		]);

		$this->add('closed_end', 'App.EndDate', [
			'fields' => 'closed',
			'filterEmpty' => true,
		]);

		$this->add('closed_by', 'App.NullableId', [
			'fields' => 'closed_by',
			'filterEmpty' => true,
		]);

		$this->add('status', 'Search.Callback', [
			'callback' => function ($query, $args, $manager) {
				if (isset($args['status'])) {
					switch ($args['status']) {
						case CaseEntity::STATUS_OPEN:
							$query->find('open', $args);
							break;
						case CaseEntity::STATUS_CLOSED:
							$query->find('closed', $args);
							break;
					}
				}

				return true;
			},
			'filterEmpty' => true,
		]);

		$this->add('empty', 'Search.Callback', [
			'callback' => function ($query, $args, $manager) {
				if (isset($args['empty'])) {
					switch (json_decode($args['empty'])) {
						case true:
							$query->find('withoutAppeals', $args);
							break;
						case false:
							$query->find('havingAppeals', $args);
							break;
					}

					return true;
				}

				return false;
			},
			'filterEmpty' => true,
		]);

		$this->add('unable_to_complete', 'App.Boolean', [
			'fields' => 'unable_to_complete',
			'filterEmpty' => true,
		]);

		/**
		 * Appeal Filters
		 */

		$this->add('appeal_level_id', 'App.Matching', [
			'model' => 'Appeals',
			'fields' => 'Appeals.appeal_level_id',
			'filterEmpty' => true,
		]);
	}
}
