<?php

use Migrations\BaseMigration;

class AddIsPassiveToUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->changeColumn('is_admin', 'boolean', [
            'default' => 0,
            'limit' => null,
            'null' => false,
        ]);
        $table->changeColumn('is_active', 'boolean', [
            'default' => 0,
            'limit' => null,
            'null' => false,
            'after' => 'is_admin',
        ]);
        $table->addColumn('is_passive', 'boolean', [
            'default' => 0,
            'limit' => null,
            'null' => false,
            'after' => 'is_active',
        ]);
        $table->update();
    }
}
