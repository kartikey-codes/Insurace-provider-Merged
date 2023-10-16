<?php

declare(strict_types=1);

namespace App\Form;

use App\Lib\PasswordUtility\PasswordUtility;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

/**
 * User Registration Form
 */
class UserRegisterForm extends Form
{
	use LocatorAwareTrait;

	/**
	 * @inheritDoc
	 */
	protected function _buildSchema(Schema $schema): Schema
	{
		return $schema
			->addField('first_name', ['type' => 'string'])
			->addField('last_name', ['type' => 'string'])
			->addField('email', ['type' => 'string'])
			->addField('password', ['type' => 'string'])
			->addField('confirm_password', ['type' => 'string'])
			->addField('invite_token', ['type' => 'string'])
			->addField('accept_terms', ['type' => 'boolean']);
	}

	/**
	 * @inheritDoc
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->notEmptyString('first_name', __('First name is required'))
			->minLength('first_name', 2)
			->maxLength('first_name', 48);

		$validator
			->notEmptyString('last_name', __('Last name is required'))
			->minLength('last_name', 2)
			->maxLength('last_name', 48);

		$validator
			->notEmptyString('email', __('Email is required'))
			->email('email')
			->minLength('email', 5)
			->maxLength('email', 80)
			->add('email', 'unique', [
				'rule' => function ($data, $provider) {
					$users = $this->fetchTable('Users');

					$user = $users->find('all', [
						'fields' => ['id', 'email'],
						'skipTenantCheck' => true,
					])->where([
						'email' => $data,
					])->first();

					if (!empty($user)) {
						return __('This email address is already registered');
					}

					return true;
				},
			]);

		$minLength = PasswordUtility::getMinimumLength();

		$validator
			//->requirePresence('Password', 'create')
			->notEmptyString('password', __('Password cannot be empty.'))
			->add('password', [
				'minLength' => [
					'rule' => ['minLength', $minLength],
					'message' => __('Passwords need to be at least {0} characters long.', $minLength),
				],
				'isCommonlyUsed' => [
					'rule' => function ($value, $context) {
						return PasswordUtility::isCommonlyUsed($value) ? false : true;
					},
					'message' => __('This password is commonly used and is insecure. Please choose a different password.'),
				],
			]);

		$validator
			->add(
				'confirm_password',
				'compareWith',
				[
					'rule' => ['compareWith', 'password'],
					'message' => __('Password and confirm password must match.'),
				]
			)
			//->requirePresence('confirm_password', 'create')
			->notEmptyString('confirm_password', __('Password cannot be empty.'))
			->add('confirm_password', [
				'minLength' => [
					'rule' => ['minLength', $minLength],
					'message' => __('Passwords need to be at least {0} characters long.', $minLength),
				],
				'isCommonlyUsed' => [
					'rule' => function ($value, $context) {
						return PasswordUtility::isCommonlyUsed($value) ? false : true;
					},
					'message' => __('This password is commonly used and is insecure. Please choose a different password.'),
				],
			]);

		$validator
			->allowEmptyString('inviteToken');

		$validator
			->boolean('accept_terms')
			->requirePresence('accept_terms', 'create')
			->equals('accept_terms', 1, __('You must accept the terms and conditions to register'));

		return $validator;
	}
}
