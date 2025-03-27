<?php

namespace App\Jobs;

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
    public function handle(): void
    {
        // Load and parse the CSV
        $csv = Reader::createFromPath($this->filePath, 'r');
        $csv->setHeaderOffset(0); // First row as header
        $csv->setEscape(''); // Avoid deprecation warning in PHP 8.4+

        $records = (new Statement())->process($csv);

        foreach ($records as $record) {
            Task::create([
                'title'       => $record['Title'] ?? 'Untitled',
                'description' => $record['Description'] ?? '',
                'status'      => $record['Status'] ?? 'TODO',
                'due_date'    => $record['Due Date'] ?? now(),
                'assigned_to' => $this->getUserIdByName($record['Assigned To'] ?? null),
                'created_by'  => $this->getUserIdByName($record['Created By'] ?? null),
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
