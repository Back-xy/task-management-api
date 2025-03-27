<?php

namespace App\Jobs;

use App\Models\ImportStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath Path to the CSV file
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     * Reads tasks from a CSV file and inserts them into the database.
     */
    public function handle()
    {
        // Create a new import status record to track progress
        $status = ImportStatus::create([
            'file' => basename($this->filePath),
            'status' => 'processing',
        ]);

        try {
            // Load and parse the CSV file
            $csv = Reader::createFromPath($this->filePath, 'r');
            $csv->setHeaderOffset(0); // First row is the header
            $csv->setEscape(''); // Avoids deprecation warning

            // Convert CSV records to array and store total rows
            $records = iterator_to_array($csv->getRecords());
            $status->total_rows = count($records);
            $status->save();

            // Insert each record into the tasks table
            foreach ($records as $record) {
                Task::create([
                    'title'       => $record['title'] ?? 'Untitled',
                    'description' => $record['description'] ?? '',
                    'status'      => $record['status'] ?? 'TODO',
                    'due_date'    => $record['due_date'] ?? now()->addWeek(),
                    'assigned_to' => $record['assigned_to'] ?? null,
                    'created_by'  => $record['created_by'] ?? 1,
                ]);

                // Increment the number of processed rows
                $status->increment('processed_rows');
            }

            // Mark the import as completed
            $status->update(['status' => 'completed']);
        } catch (\Throwable $e) {
            // On error, mark import as failed and store the error message
            $status->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve a user ID by their name.
     *
     * @param string|null $name
     * @return int|null
     */
    protected function getUserIdByName(?string $name): ?int
    {
        if (! $name) {
            return null;
        }

        return User::where('name', $name)->value('id');
    }
}
