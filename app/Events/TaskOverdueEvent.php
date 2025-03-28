<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TaskOverdueEvent implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Task $task) {}

    /**
     * Define the private channel the event will broadcast on.
     * Only the Product Owner who created the task will receive the alert.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->task->created_by)
        ];
    }

    /**
     * Custom name for the broadcast event.
     */
    public function broadcastAs(): string
    {
        return 'task.overdue';
    }

    /**
     * Data to send along with the broadcast event.
     */
    public function broadcastWith()
    {
        return [
            'task' => [
                'id'       => $this->task->id,
                'title'    => $this->task->title,
                'due_date' => \Carbon\Carbon::parse($this->task->due_date)->toDateString(),
                'message'  => 'This task is overdue!',
            ],
        ];
    }
}
