<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Cake\I18n\FrozenTime;
use Search\Model\Filter\Base;

class EndDate extends Base
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

		$endDate = (new FrozenTime($this->value()))
			->addHours(23)
			->addMinutes(59)
			->addSeconds(59);

		foreach ($this->fields() as $field) {
			$this->getQuery()->andWhere(function ($exp, $q) use ($field, $endDate) {
				return $exp->lte($field, $endDate->format(FrozenTime::DEFAULT_TO_STRING_FORMAT));
			});
		}

		return true;
	}
}
