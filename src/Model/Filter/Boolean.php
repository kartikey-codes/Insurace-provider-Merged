<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\Base;

class Boolean extends Base
{
	/**
	 * Process a comparison-based condition (e.g. column BETWEEN Date1 AND Date2).
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

		$boolean = (bool)json_decode($this->value());

		foreach ($this->fields() as $field) {
			$this->getQuery()->andWhere(function ($exp, $q) use ($field, $boolean) {
				return $exp->eq($field, $boolean);
			});
		}

		return true;
	}
}
