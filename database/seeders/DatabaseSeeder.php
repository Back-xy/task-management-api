<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Create roles
        User::factory()->count(3)->state(['role' => 'product_owner'])->create();
        User::factory()->count(5)->state(['role' => 'developer'])->create();
        User::factory()->count(3)->state(['role' => 'tester'])->create();

        // Create top-level tasks
        $tasks = Task::factory()->count(10)->create();

        // Add subtasks for each task
        foreach ($tasks as $task) {
            Task::factory()->count(2)->create([
                'parent_id' => $task->id,
                'created_by' => $task->created_by,
            ]);
        }

        // Create some task logs
        foreach (Task::all() as $task) {
            TaskLog::create([
                'task_id' => $task->id,
                'changed_by' => $task->created_by,
                'field_changed' => 'status',
                'old_value' => 'TODO',
                'new_value' => $task->status,
            ]);
        }
    }
}
