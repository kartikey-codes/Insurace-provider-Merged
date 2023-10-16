<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class MakeDenialAndNotDefendableReasonsGlobal extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 * @return void
	 */
	public function change()
	{
		$denialReasons = $this->table('denial_reasons');

		if ($denialReasons->hasForeignKey('client_id')) {
			$denialReasons
				->dropForeignKey('client_id')
				->save();
		}

		if ($denialReasons->hasIndex('client_id')) {
			$denialReasons
				->removeIndex(['client_id'])
				->save();
		}

		if ($denialReasons->hasColumn('client_id')) {
			$denialReasons
				->removeColumn('client_id')
				->save();
		}

		$notDefendableReasons = $this->table('not_defendable_reasons');

		if ($notDefendableReasons->hasForeignKey('client_id')) {
			$notDefendableReasons
				->dropForeignKey('client_id')
				->save();
		}

		if ($notDefendableReasons->hasIndex('client_id')) {
			$notDefendableReasons
				->removeIndex(['client_id'])
				->save();
		}

		if ($notDefendableReasons->hasColumn('client_id')) {
			$notDefendableReasons
				->removeColumn('client_id')
				->save();
		}
	}
}
