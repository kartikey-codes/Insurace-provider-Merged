<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class DropIdColumnFromInsuranceProviderAppealLevels extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
{
    $table = $this->table('insurance_provider_appeal_levels');
    $table->removeColumn('id');
    $table->update();
}

}
