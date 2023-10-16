<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\Entity\CaseEntity;
use App\Model\Table\UsersTable;
use App\Model\Table\CaseTypesTable;
use App\Model\Table\ClientEmployeesTable;
use App\Model\Table\DenialTypesTable;
use App\Model\Table\FacilitiesTable;
use App\Model\Table\InsuranceProvidersTable;
use App\Model\Table\PatientsTable;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use InvalidArgumentException;
use LengthException;

/**
 * @property \Faker\Generator $faker
 */
class CaseFactory extends AbstractFactory
{
	/** @var \App\Model\Table\UsersTable */
	private UsersTable $users;

	/** @var \App\Model\Table\CaseTypesTable */
	private CaseTypesTable $caseTypes;

	/** @var \App\Model\Table\ClientEmployeesTable */
	private ClientEmployeesTable $clientEmployees;

	/** @var \App\Model\Table\DenialTypesTable */
	private DenialTypesTable $denialTypes;

	/** @var \App\Model\Table\FacilitiesTable */
	private FacilitiesTable $facilities;

	/** @var \App\Model\Table\InsuranceProvidersTable */
	private InsuranceProvidersTable $insuranceProviders;

	/** @var \App\Model\Table\PatientsTable */
	private PatientsTable $patients;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->users = $this->fetchTable('Users');
		$this->caseTypes = $this->fetchTable('CaseTypes');
		$this->clientEmployees = $this->fetchTable('ClientEmployees');
		$this->denialTypes = $this->fetchTable('DenialTypes');
		$this->facilities = $this->fetchTable('Facilities');
		$this->insuranceProviders = $this->fetchTable('InsuranceProviders');
		$this->patients = $this->fetchTable('Patients');
	}

	/**
	 * Get random entities to use as associations
	 *
	 * @return array
	 */
	private function getRandomAssociations(): array
	{
		return [
			'User' => $this->users->getRandomByClientId($this->client->id),
			'CaseType' => $this->caseTypes->getRandomFromAll(),
			'CaseOutcomes' => $this->caseTypes->getRandomFromAll(),
			'ClientEmployee' => $this->clientEmployees->getRandomByClientId($this->client->id),
			'DenialType' => $this->denialTypes->getRandomFromAll(),
			'Facility' => $this->facilities->getRandomByClientId($this->client->id),
			'InsuranceProvider' => $this->insuranceProviders->getRandomByClientId($this->client->id),
			'Patient' => $this->patients->getRandomByClientId($this->client->id)
		];
	}

	/**
	 * Generate a faked patient for use in testing or demo data
	 *
	 * @return CaseEntity
	 * @throws InvalidArgumentException
	 * @throws LengthException
	 */
	public function fake(): CaseEntity
	{
		$randoms = $this->getRandomAssociations($this->client->id);

		$maxCurrency = 1000000;

		$admitDate = new FrozenDate($this->faker->date());
		$dischargeDate = $admitDate->addDays(rand(1, 90));

		// Chance of being assigned
		if ($this->faker->boolean()) {
			$assigned = new FrozenTime($this->faker->time());
			$assignedTo = $randoms['User']->id;
		} else {
			$assigned = null;
			$assignedTo = null;
		}

		// Chance of being closed
		if ($this->faker->boolean()) {
			$closed = new FrozenTime($this->faker->time());
			$closedBy = $randoms['User']->id;
			$caseOutcomeId = 1; // @todo Random
		} else {
			$closed = null;
			$closedBy = null;
			$caseOutcomeId = null;
		}

		return new CaseEntity([
			'client_id' => $this->client->id,
			'case_type_id' => 1,
			'patient_id' => $randoms['Patient']->id,
			'denial_type_id' => $randoms['DenialType']->id,
			'facility_id' => $randoms['Facility']->id,
			'case_outcome_id' => $caseOutcomeId,
			'insurance_provider_id' => $randoms['InsuranceProvider']->id,
			'insurance_type_id' => $randoms['InsuranceProvider']->default_insurance_type_id,
			'insurance_plan' => $this->faker->lexify('?????????????????'),
			'total_claim_amount' => $this->faker->boolean() ? rand(0, $maxCurrency) : null,
			'reimbursement_amount' => $this->faker->boolean() ? rand(0, $maxCurrency) : null,
			'disputed_amount' => $this->faker->boolean() ? rand(0, $maxCurrency) : null,
			'settled_amount' => $this->faker->boolean() ? rand(0, $maxCurrency) : null,
			'insurance_number' => $this->faker->boolean() ? $this->faker->bothify('FAKE-?#####') : null,
			'visit_number' => $this->faker->boolean() ? $this->faker->bothify('FAKE_######') : null,
			'admit_date' => $admitDate,
			'discharge_date' => $dischargeDate,
			'closed' => $closed,
			'closed_by' => $closedBy,
			'assigned' => $assigned,
			'assigned_to' => $assignedTo,
			'client_employee_id' => $randoms['ClientEmployee']->id,
		]);
	}
}
