<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Events\TaskOverdueEvent;
use Illuminate\Support\Facades\Log;

class CheckOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tasks:check-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Check and alert for overdue tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find tasks that are overdue and haven't triggered an alert yet
        Task::where('due_date', '<', now())
            ->whereNotIn('status', ['DONE', 'REJECTED'])
            ->whereNull('overdue_notified_at')
            ->each(function ($task) {
                // Broadcast the overdue task alert
                broadcast(new TaskOverdueEvent($task));

                // Mark the task as notified to avoid re-alerting
                $task->update(['overdue_notified_at' => now()]);
            });
    }
}
