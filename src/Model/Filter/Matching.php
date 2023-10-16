<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\Base;

class Matching extends Base
{
	/**
	 * Used for matching a model based on an ID
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
		$model = $this->getConfig('model');

		foreach ($this->fields() as $field) {
			$this->getQuery()->matching($model, function ($q) use ($model, $field, $value) {
				return $q->where([
					$field => $value,
				]);
			});
		}

		return true;
	}
}
