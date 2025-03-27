<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use SplTempFileObject;

class ExportTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:tasks {--path=storage/app/tasks.csv : File path to export to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all tasks to a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Generate unique filename using current timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "tasks_{$timestamp}.csv";

        // Define export directory and ensure it exists
        $exportDir = storage_path('app/exports');
        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $filePath = "{$exportDir}/{$fileName}";

        // Fetch all tasks with related user info
        $tasks = Task::with(['assignee:id,name', 'creator:id,name'])->get();

        // Create CSV writer using in-memory temporary file
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setEscape(''); // Avoid PHP 8.4+ deprecation warning

        // Insert header row
        $csv->insertOne(['ID', 'Title', 'Description', 'Status', 'Due Date', 'Assigned To', 'Created By']);

        // Insert task rows
        foreach ($tasks as $task) {
            $csv->insertOne([
                $task->id,
                $task->title,
                $task->description,
                $task->status,
                $task->due_date,
                $task->assignee?->name ?? '',
                $task->creator?->name ?? '',
            ]);
        }

        // Save to file and confirm
        file_put_contents($filePath, (string) $csv);
        $this->info("Tasks exported to: storage/app/exports/{$fileName}");
    }
}
