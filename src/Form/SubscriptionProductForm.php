<?php

declare(strict_types=1);

namespace App\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * Subscription Product Select Form
 */
class SubscriptionProductForm extends Form
{
	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('product_id', ['type' => 'string'])
			->addField('licenses', ['type' => 'number']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->notEmptyString('product_id');

		$validator
			->add('licenses', 'valid', [
				'rule' => function ($value, $context) {
					if (!$value) {
						return false;
					}

					if ($value < 1) {
						return 'At least 1 license is required.';
					}

					$max = Configure::read('Subscriptions.maxLicenses');

					if ($value > $max) {
						return __('Please contact us if you need over {0} licenses.', $max);
					}

					return true;
				},
				'message' => 'Number of licenses must be provided.',
			]);

		return $validator;
	}

	/**
	 * @inheritDoc
	 */
	protected function _execute(array $data): bool
	{
		return true;
	}
}
