<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddColumnsToInsuranceProviderAppealLevels extends AbstractMigration
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
        $table
        ->addColumn('Grace_days', 'integer', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn('level_type', 'string', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn('decision_options', 'integer', [
            'default' => null,
            'null' => true,
        ])
        ->update();
    }
}
