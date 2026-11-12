<?php

use Migrations\BaseMigration;

class DefaultIsPseudoToSongs extends BaseMigration
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
        $table->changeColumn('is_pseudo', 'boolean', [
            'default' => false,
            'limit' => null,
            'null' => false,
        ]);
        $table->update();
    }
}
