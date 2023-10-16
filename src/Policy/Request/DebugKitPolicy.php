<?php

declare(strict_types=1);

namespace App\Policy\Request;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;

class DebugKitPolicy implements RequestPolicyInterface
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
		// Debug Kit
		if (Configure::read('debug')) {
			// Disable authorization for DebugKit
			if ($request->getParam('plugin') === 'DebugKit') {
				return true;
			}
		}

		return null;
	}
}
