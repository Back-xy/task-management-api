<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskLog;
use Illuminate\Database\Seeder;

class TaskLogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Task::all() as $task) {
            TaskLog::create([
                'task_id'       => $task->id,
                'changed_by'    => $task->created_by,
                'field_changed' => 'status',
                'old_value'     => 'TODO',
                'new_value'     => $task->status,
            ]);
        }
    }
}
