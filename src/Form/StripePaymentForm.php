<?php

declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * Stripe Payment Form
 */
class StripePaymentForm extends Form
{
	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('customer_id', ['type' => 'string'])
			->addField('product_id', ['type' => 'string']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->notEmptyString('customer_id');

		$validator
			->notEmptyString('product_id');

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
