<?php

declare(strict_types=1);

namespace App\Policy\Request;

use Authorization\IdentityInterface;
use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;

class AreaPolicy implements RequestPolicyInterface
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
		if (empty($identity)) {
			return null;
		}

		/**
		 * @var \App\Model\Entity\User $user
		 */
		$user = $identity->getOriginalData();

		// Admins can access all areas
		if ($user->isAdmin()) {
			return true;
		}

		// Admin Area
		if ($request->is('admin') && $user->isAdmin() == false) {
			return false;
		}

		// Client Area
		if ($request->is('client') && $user->isClientUser() == false) {
			return false;
		}

		// Vendor Area
		if ($request->is('vendor') && $user->isVendorUser() == false) {
			return false;
		}

		// Default
		return true;
	}
}
