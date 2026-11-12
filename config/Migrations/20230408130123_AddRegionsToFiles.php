<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddRegionsToFiles extends BaseMigration
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
        $table = $this->table('files');
        $table->addColumn('regions', 'text', [
            'default' => null,
            'limit' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
