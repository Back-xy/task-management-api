<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Store a new task (Product Owner only)
     */
    public function store(Request $request)
    {
        // Only Product Owner can create tasks
        if (Auth::user()->role !== 'product_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_id'   => 'nullable|exists:tasks,id',
        ]);

        $task = Task::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'due_date'    => $validated['due_date'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'parent_id'   => $validated['parent_id'] ?? null,
            'status'      => 'TODO',
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task'    => $task,
        ], 201);
    }

    /**
     * Show task details with subtasks and logs
     */
    public function show($id)
    {
        $task = Task::with([
            'subtasks',
            'logs',
            'assignee:id,name,email',
            'creator:id,name,email',
            'parent:id,title'
        ])->find($id);

        if (! $task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }
}
