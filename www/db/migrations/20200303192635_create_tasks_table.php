<?php

use Phinx\Migration\AbstractMigration;

class CreateTasksTable extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('task', ['signed' => false]);
        $table->addColumn('task', 'string', [
            'limit'   => 255,
            'null'    => true,
        ])->addColumn('is_done', 'boolean', [
            'default' => 0,
            'limit'   => 1,
            'signed'  => false,
            'null'    => true,
        ])
        ->addColumn('is_deleted', 'boolean', [
            'default' => 0,
            'limit'   => 1,
            'signed'  => false,
            'null'    => true,
        ])
        ->addColumn('created', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP'
        ])
        ->addColumn('modified', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP',
            'update'  => 'CURRENT_TIMESTAMP',
        ])
        ->create();
    }

}
