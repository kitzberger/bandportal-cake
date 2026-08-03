<?php

use Migrations\BaseMigration;

class NullableFieldsInSongs extends BaseMigration
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
        $table = $this->table('songs');
        $table->changeColumn('url', 'string', ['default' => null, 'limit' => 255, 'null' => true]);
        $table->update();
    }
}
