<?php

use Illuminate\Support\Facades\Route;

// Redirect the root URL to the login page
Route::get('/', function () {
    return redirect('/login');
});

// Shows the login page view
Route::view('/login', 'login');

// Shows the test page for overdue WebSocket alerts
Route::view('/test-alert', 'test-alert');
