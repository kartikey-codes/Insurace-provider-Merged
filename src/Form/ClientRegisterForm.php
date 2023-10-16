<?php

declare(strict_types=1);

namespace App\Form;

use App\Exception\NpiServiceRequestException;
use App\Lib\NpiUtility\NpiUtility;
use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

/**
 * Client Registration Form
 */
class ClientRegisterForm extends Form
{
	use LocatorAwareTrait;

	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('name', ['type' => 'string'])
			->addField('npi_number', ['type' => 'number'])
			->addField('email', ['type' => 'string'])
			->addField('phone', ['type' => 'string'])
			->addField('accept_terms', ['type' => 'boolean']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->add('name', 'unique', [
				'last' => true,
				'rule' => function ($data, $provider) {
					$clients = $this->fetchTable('Clients');

					$client = $clients->find('all', [
						'fields' => ['id', 'name'],
						'skipTenantCheck' => true,
						'skipVendorCheck' => true
					])->where([
						'name' => $data,
					])
						->first();

					if (!empty($client)) {
						return __('This name is already registered');
					}

					return true;
				},
			])
			->notEmptyString('name');

		$validator
			->notEmptyString('email', __('Email is required'))
			->email('email')
			->minLength('email', 5)
			->maxLength('email', 80)
			->add('email', 'unique', [
				'rule' => function ($data, $provider) {
					$clients = $this->fetchTable('Clients');
					$client = $clients->find('all', [
						'fields' => ['email'],
						'skipTenantCheck' => true,
						'skipVendorCheck' => true
					])->where([
						'email' => $data,
					])->first();

					if (!empty($client)) {
						return __('This email address is already registered');
					}

					return true;
				},
			]);

		$validator
			->add('phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a number in (###) ###-#### format'),
			])
			->minLength('phone', 5)
			->maxLength('phone', 50)
			->allowEmptyString('phone');

		$validator
			->notEmptyString('npi_number', __('NPI number is required'))
			->lengthBetween('npi_number', [10, 10], __('NPI number must be 10 digits'))
			->add('npi_number', 'numeric', [
				'rule' => 'numeric',
			])
			->add('npi_number', 'unique', [
				'rule' => function ($data, $provider) {
					$clients = $this->fetchTable('Clients');

					$client = $clients->find('all', [
						'fields' => ['id', 'npi_number'],
						'skipTenantCheck' => true,
						'skipVendorCheck' => true
					])->where([
						'npi_number' => $data,
					])
						->first();

					if (!empty($client)) {
						return __('This NPI number is already registered');
					}

					return true;
				},
			])
			->add('npi_number', 'valid', [
				'rule' => function ($data, $provider) {
					// Allow configuration to control whether NPI API is validated
					$validate = Configure::read('NPI.validateClients', true);
					if (empty($validate)) {
						return true;
					}

					// Validate NPI number exists in database
					try {
						return NpiUtility::isValidOrganization(intval($data));
					} catch (NpiServiceRequestException $e) {
						return $e->getMessage();
					}
				},
				'message' => __('This NPI number is invalid in the NPI database'),
			]);

		$validator
			->boolean('accept_terms')
			->requirePresence('accept_terms', 'create')
			->equals('accept_terms', 1, __('You must accept the terms and conditions to register.'));

		return $validator;
	}
}
