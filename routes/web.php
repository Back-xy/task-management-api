<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/login', 'login');          // Shows the login page
Route::view('/test-alert', 'test-alert'); // Shows the test page for overdue alerts