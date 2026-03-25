<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddDefaultZoomToSongs extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('songs');
        $table->addColumn('zoom_full', 'decimal', [
            'default' => '1.00',
            'limit' => null,
            'null' => true,
            'precision' => 3,
            'scale' => 2,
        ]);
        $table->addColumn('zoom_text', 'decimal', [
            'default' => '1.00',
            'limit' => null,
            'null' => true,
            'precision' => 3,
            'scale' => 2,
        ]);
        $table->addColumn('zoom_chords', 'decimal', [
            'default' => '1.00',
            'limit' => null,
            'null' => true,
            'precision' => 3,
            'scale' => 2,
        ]);
        $table->update();
    }
}
