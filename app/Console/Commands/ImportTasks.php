<?php

namespace App\Console\Commands;

use App\Jobs\ImportTasksJob;
use Illuminate\Console\Command;

class ImportTasks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:tasks {file : The path to the CSV file inside storage/app}';

    /**
     * The console command description.
     */
    protected $description = 'Import tasks from a CSV file using a background job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Build the full file path
        $filePath = storage_path('app/' . $this->argument('file'));

        // Check if the file exists
        if (!file_exists($filePath)) {
            $this->error("File not found at: $filePath");
            return 1;
        }

        // Dispatch the import job
        ImportTasksJob::dispatch($filePath);

        // Notify the user that the job has been dispatched
        $this->info("Import job has been dispatched for file: {$this->argument('file')}");
        return 0;
    }
}
