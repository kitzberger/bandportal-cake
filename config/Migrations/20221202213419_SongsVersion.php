<?php

use Migrations\BaseMigration;

class SongsVersion extends BaseMigration
{
    public function up()
    {
        $this->table('songs_versions')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('song_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('text', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addForeignKey(
                'song_id',
                'songs',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->create();
    }

    public function down()
    {
        $this->table('songs_versions')
            ->dropForeignKey(
                'collection_id'
            )
            ->dropForeignKey(
                'file_id'
            );

        $this->dropTable('songs_versions');
    }
}
