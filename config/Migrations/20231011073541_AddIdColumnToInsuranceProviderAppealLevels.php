<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddIdColumnToInsuranceProviderAppealLevels extends AbstractMigration
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
        $table = $this->table('insurance_provider_appeal_levels');
        $table->addColumn('id', 'integer', [
            'default' => 1,
            'limit' => 11,
            'null' => false,
        ]);
        $table->update();
    }
}
