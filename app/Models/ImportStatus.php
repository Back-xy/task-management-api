<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    protected $fillable = [
        'file',
        'status',
        'processed_rows',
        'total_rows',
        'error_message',
    ];
}
