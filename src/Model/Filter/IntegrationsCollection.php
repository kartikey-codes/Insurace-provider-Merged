<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

class IntegrationsCollection extends FilterCollection
{
	/**
	 * Initialize search filters
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		$this->add('name', 'Search.Value', [
			'fields' => 'integration_name',
			'filterEmpty' => true,
		]);
	}
}
