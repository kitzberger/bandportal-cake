<?php

use Migrations\BaseMigration;

class NullableFieldsInLogs extends BaseMigration
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
        $table = $this->table('logs');
        $table->changeColumn('song_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('date_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('idea_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('comment_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('collection_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('vote_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('file_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->changeColumn('share_id', 'integer', ['default' => null, 'limit' => 11, 'null' => true]);
        $table->update();
    }
}
