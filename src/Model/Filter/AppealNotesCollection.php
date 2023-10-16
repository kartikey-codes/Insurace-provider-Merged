<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

class AppealNotesCollection extends FilterCollection
{
	/**
	 * Initialize search filters
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		$this->add('search', 'Search.Like', [
			'fields' => [
				'notes',
			],
			'before' => true,
			'after' => true,
			'filterEmpty' => true,
		]);
	}
}
