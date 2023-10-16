<?php

declare(strict_types=1);

namespace App\Policy\Request;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;

class AuthControllerPolicy implements RequestPolicyInterface
{
	/**
	 * Method to check if the request can be accessed
	 *
	 * @param \Authorization\IdentityInterface|null Identity
	 * @param \Cake\Http\ServerRequest $request Server Request
	 * @return ?bool
	 */
	public function canAccess(?IdentityInterface $identity, ServerRequest $request): ?bool
	{
		// Always allow access to login/logout type actions

		$prefix = $request->getParam('prefix');
		$controller = $request->getParam('controller');

		return $prefix == null && $controller === 'Auth' ? true : null;
	}
}
