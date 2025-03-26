<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::factory()->count(10)->create();

        foreach ($tasks as $task) {
            Task::factory()->count(2)->create([
                'parent_id'   => $task->id,
                'created_by'  => $task->created_by,
            ]);
        }
    }
}
