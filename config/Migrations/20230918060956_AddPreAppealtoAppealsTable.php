<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddPreAppealtoAppealsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('appeals');
        $table->addColumn('pre_appeal', 'boolean', [
            'default' => false, // Default value for boolean (true/false)
            'null' => true,
        ]);
        $table->update();
    }
}
