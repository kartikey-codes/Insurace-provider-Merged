<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveBlurbsAndTags extends AbstractMigration
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
		$this->table('tags')
			->removeIndex(['client_id'])
			->save();

		$this->table('tags')
			->dropForeignKey('client_id')
			->save();

		$this->table('tags')
			->drop()
			->save();

		$this->table('blurb_tags')
			->removeIndex(['client_id'])
			->save();

		$this->table('blurb_tags')
			->dropForeignKey('client_id')
			->save();

		$this->table('blurb_tags')
			->drop()
			->save();

		$this->table('blurb_categories')
			->removeIndex(['client_id'])
			->save();

		$this->table('blurb_categories')
			->dropForeignKey('client_id')
			->save();

		$this->table('blurb_categories')
			->drop()
			->save();

		$this->table('blurbs')
			->removeIndex(['client_id'])
			->save();

		$this->table('blurbs')
			->dropForeignKey('client_id')
			->save();

		$this->table('blurbs')
			->drop()
			->save();
	}
}
