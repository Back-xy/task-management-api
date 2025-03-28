<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Schedule the custom command to check for overdue tasks every 10 seconds
Schedule::command('tasks:check-overdue')->everyTenSeconds();
