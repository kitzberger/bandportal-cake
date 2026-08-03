<?php

use Migrations\BaseMigration;

class NullableFieldsInDates extends BaseMigration
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
        $table = $this->table('dates');
        $table->changeColumn('uri', 'string', ['default' => null, 'limit' => 255, 'null' => true]);
        $table->changeColumn('location_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->update();
    }
}
