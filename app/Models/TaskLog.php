<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    /** @use HasFactory<\Database\Factories\TaskLogFactory> */
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'task_id',
        'changed_by',
        'field_changed',
        'old_value',
        'new_value',
    ];

    /**
     * The task associated with this log entry.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * The user who made the change.
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
