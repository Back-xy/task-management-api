<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);

// User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->can('viewAny', User::class);

    Route::post('/users', [UserController::class, 'store'])
        ->can('create', User::class);

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->can('update', 'user');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->can('delete', 'user');
});

// Task Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])
        ->can('viewAny', Task::class);

    Route::post('/tasks', [TaskController::class, 'store'])
        ->can('create', Task::class);

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->can('view', 'task');

    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->can('update', 'task');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->can('delete', 'task');
});

// Import Status Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/import-status/{id}', [ImportStatusController::class, 'show']);
});
