<?php

use Migrations\BaseMigration;

class NullableFieldsInComments extends BaseMigration
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
        $table = $this->table('comments');
        $table->changeColumn('collection_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->update();
    }
}
