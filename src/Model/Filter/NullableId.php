<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\Base;

class NullableId extends Base
{
	/**
	 * Used for ID fields where false equals an explicit null value.
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

		foreach ($this->fields() as $field) {
			if ($value === false) {
				$this->getQuery()->andWhere(function ($exp, $q) use ($field, $value) {
					return $exp->isNull($field);
				});
			} else {
				$this->getQuery()->andWhere(function ($exp, $q) use ($field, $value) {
					return $exp->eq($field, $value);
				});
			}
		}

		return true;
	}
}
