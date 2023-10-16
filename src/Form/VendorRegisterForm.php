<?php

declare(strict_types=1);

namespace App\Form;

use App\Lib\LocationUtility\LocationUtility;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

/**
 * Vendor Registration Form
 */
class VendorRegisterForm extends Form
{
	use LocatorAwareTrait;

	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('name', ['type' => 'string'])
			->addField('phone', ['type' => 'string'])
			->addField('street_address_1', ['type' => 'string'])
			->addField('street_address_2', ['type' => 'string'])
			->addField('city', ['type' => 'string'])
			->addField('state', ['type' => 'string'])
			->addField('zip', ['type' => 'string'])
			->addField('accept_terms', ['type' => 'boolean']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->notEmptyString('phone')
			->minLength('phone', 5)
			->maxLength('phone', 50)
			->add('phone', 'validFormat', [
				'rule' => ['custom', '/^\(\d{3}\) \d{3}-\d{4}$/'],
				'message' => __('Please enter a number in (###) ###-#### format'),
			]);

		$validator
			->add('name', 'unique', [
				'rule' => function ($data, $provider) {
					$vendors = $this->fetchTable('Vendors');

					$vendor = $vendors->find('all', [
						'fields' => ['id', 'name'],
						'skipTenantCheck' => true,
						'skipVendorCheck' => true
					])->where([
						'name' => $data,
					])
						->first();

					if (!empty($vendor)) {
						return __('This name is already registered');
					}

					return true;
				},
			])
			->notEmptyString('name');

		$validator
			->notEmptyString('street_address_1', __('Address is required'));

		$validator
			->notEmptyString('city', __('City is required'));

		$validator
			->add('state', 'custom', [
				'rule' => function ($value, $context) {
					$stateValues = array_keys(LocationUtility::states());

					return in_array($value, $stateValues);
				},
				'message' => __('The state is not valid'),
			])
			->notEmptyString('state');

		$validator
			->notEmptyString('zip', __('Zip code is required'));

		$validator
			->boolean('accept_terms')
			->requirePresence('accept_terms', 'create')
			->equals('accept_terms', 1, __('You must accept the terms and conditions to register'));

		return $validator;
	}
}
