<?php

use Migrations\BaseMigration;

class DefaultIsPublicToFiles extends BaseMigration
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
        $table = $this->table('files');
        $table->changeColumn('is_public', 'boolean', [
            'default' => false,
            'limit' => null,
            'null' => false,
        ]);
        $table->update();
    }
}
