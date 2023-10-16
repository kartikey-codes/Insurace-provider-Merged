<?php

declare(strict_types=1);

namespace App\Model\Filter\Rule;

use Search\Model\Filter\Base;

class NotMatchingAssociation extends Base
{
	/**
	 * Used for checking if a model is not associated with another.
	 * Mostly used for users not assigned to roles.
	 *
	 * Pass null to skip this filter.
	 *
	 * @return bool
	 */
	public function process(): bool
	{
		if ($this->skip()) {
			return false;
		}

		if (is_null($this->value())) {
			return false;
		}

		$value = json_decode($this->value());
		$association = $this->getConfig('association');

		$this->getQuery()->notMatching($association, function ($q) use ($association, $value) {
			return $q->where([
				'id' => $value
			]);
		});

		return true;
	}
}
