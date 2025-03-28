<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Return the authenticated user's info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --------------------
// Auth Routes
// --------------------
Route::post('/login', [AuthController::class, 'login']);


// --------------------
// User Management Routes (Product Owner only)
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // List all users
    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class);

    // Create a new user
    Route::post('/users', [UserController::class, 'store'])
        ->can('create', User::class);

    // Update an existing user
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->can('update', 'user');

    // Delete a user
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->can('delete', 'user');
});


// --------------------
// Task Management Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // List all tasks with filters
    Route::get('/tasks', [TaskController::class, 'index'])
        ->can('viewAny', Task::class);

    // Create a new task
    Route::post('/tasks', [TaskController::class, 'store'])
        ->can('create', Task::class);

    // View task details
    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->can('view', 'task');

    // Update a task (status, title, etc.)
    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->can('update', 'task');

    // Delete a task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->can('delete', 'task');
});


// --------------------
// Import Job Status Route
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // Show the status/progress of a task import job
    Route::get('/import-status/{id}', [ImportStatusController::class, 'show']);
});
