<?php

declare(strict_types=1);

namespace App\Model\Filter\Rule;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Search\Model\Filter\Base;

class AliasedFilters extends Base
{
	/**
	 * Allows combining multiple, separate search filters into one
	 * result set when using a general 'search' parameter in a model.
	 *
	 * Makes it easy to map $search<string> to multiple filters:
	 * [ 'full_name', 'display_name', 'company_name' ]
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

		$searchQuery = (string)$this->value();
		$filters = $this->getConfig('filters', []);

		// Get array of filter names with search value for each
		$params = array_combine(
			array_values($filters),
			array_fill(0, count($filters), $searchQuery)
		);

		// Currently broken when using multiple filters
		// Need to get OR used for between filters, so filters aren't exclusive
		// @todo Fix this

		$this->getQuery()->find('search', [
			'search' => $params
		]);

		return true;
	}
}
