<?php

use Migrations\BaseMigration;

class AddNotifiedToUsers extends BaseMigration
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
        $table->addColumn('notified', 'datetime', [
            'default' => null,
            'limit' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
