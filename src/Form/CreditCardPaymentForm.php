<?php

declare(strict_types=1);

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

/**
 * Credit Card Payment Form
 */
class CreditCardPaymentForm extends Form
{
	use LocatorAwareTrait;

	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('customer_id', ['type' => 'string'])
			->addField('card_number', ['type' => 'string'])
			->addField('card_exp_month', ['type' => 'integer'])
			->addField('card_exp_year', ['type' => 'integer'])
			->addField('card_cvv', ['type' => 'integer'])
			->addField('card_zip', ['type' => 'integer'])
			->addField('product_id', ['type' => 'string'])
			->addField('plan_id', ['type' => 'string']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->notEmptyString('card_name');

		$validator
			->creditCard('card_number');

		$validator
			->numeric('card_exp_month', __('Month must be numeric'))
			->inList('card_exp_month', array_values($this->expirationMonths()), __('Month must be a valid month'));

		$validator
			->numeric('card_exp_year', __('Year must be numeric'))
			->inList('card_exp_year', array_values($this->expirationYears()), __('Year must be a valid year'));

		$validator
			->numeric('card_cvv')
			->lengthBetween('card_cvv', [3, 4]);

		$validator
			->notEmptyString('card_zip');

		$validator
			->notEmptyString('plan_id');

		return $validator;
	}

	/**
	 * @inheritDoc
	 */
	protected function _execute(array $data): bool
	{
		return true;
	}

	/**
	 * Return valid credit card expiration month values
     *
     * @return array
	 */
	public function expirationMonths(): array
	{
		$range = range(1, 12);

		return array_combine($range, $range);
	}

	/**
	 * Return valid credit card expiration month values
     *
     * @return array
	 */
	public function expirationYears(): array
	{
		$year = intval(date('Y'));
		$range = range($year, $year + 10);

		return array_combine($range, $range);
	}
}
