<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;

class TaskOverdueEvent implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Task $task) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->task->created_by)
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.overdue';
    }

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
