<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddTosTaxonomyFieldsToClients extends AbstractMigration
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
        $this->table('clients')
            ->addColumn('tos_date', 'date', [
                'default' => null,
                'null' => true,
                'limit' => null
            ])
            ->addColumn('primary_taxonomy', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->save();
    }
}
