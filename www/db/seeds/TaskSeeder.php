<?php


use Phinx\Seed\AbstractSeed;

class TaskSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'task' => 'My 1st Task'],
            ['id' => 2, 'task' => 'My 2nd Task'],
            ['id' => 3, 'task' => 'My 3rd Task'],
            ['id' => 4, 'task' => 'My 4th Task'],
            ['id' => 5, 'task' => 'My 5th Task'],
        ];
        $tasks = $this->table('task');
        $tasks->truncate();
        $tasks->insert($data)->saveData();
    }
}
