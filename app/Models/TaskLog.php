<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    /** @use HasFactory<\Database\Factories\TaskLogFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'changed_by',
        'field_changed',
        'old_value',
        'new_value',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
