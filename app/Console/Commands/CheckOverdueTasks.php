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
     *
     * @var string
     */
    protected $signature = 'tasks:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and alert for overdue tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Task::where('due_date', '<', now())
            ->whereNotIn('status', ['DONE', 'REJECTED'])
            ->whereNull('overdue_notified_at')
            ->each(function ($task) {
                broadcast(new TaskOverdueEvent($task));

                $task->update(['overdue_notified_at' => now()]);
                Log::info("Updated overdue_notified_at for task: " . $task->id);
            });
    }
}
