<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'title',
        'description',
        'status',
        'assigned_to',
        'created_by',
        'parent_id',
        'due_date',
        'overdue_notified_at',
    ];

    /**
     * The user assigned to this task.
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user who created the task (usually the Product Owner).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The parent task, if this is a subtask.
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Subtasks that belong to this task.
     */
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Logs associated with this task (e.g., status changes).
     */
    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }
}
