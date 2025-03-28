<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Return a list of tasks with optional filters.
     */
    public function index(Request $request)
    {
        // Start with base query and eager-load relations
        $query = Task::with(['assignee:id,name', 'creator:id,name']);

        // Apply search filters (title, description, or ID)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        // Filter by assignee's name
        if ($assigneeName = $request->input('assignee_name')) {
            $query->whereHas('assignee', function ($q) use ($assigneeName) {
                $q->where('name', 'like', "%{$assigneeName}%");
            });
        }

        // Filter by one or more assigned user IDs
        if ($assignedTo = $request->input('assigned_to')) {
            $ids = explode(',', $assignedTo);
            $query->whereIn('assigned_to', $ids);
        }

        // Return tasks as JSON sorted by newest first
        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    /**
     * Create and store a new task.
     */
    public function store(Request $request)
    {
        // Validate incoming task data
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_id'   => 'nullable|exists:tasks,id',
        ]);

        // Create and save the task
        $task = Task::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'due_date'    => $validated['due_date'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'parent_id'   => $validated['parent_id'] ?? null,
            'status'      => 'TODO',
            'created_by'  => Auth::id(),
        ]);

        // Return success response and created task
        return response()->json([
            'message' => 'Task created successfully',
            'task'    => $task,
        ], 201);
    }

    /**
     * Show the full details of a specific task.
     */
    public function show(Task $task)
    {
        // Load related data for detailed view
        $task->load([
            'subtasks:id,title,description,status,assigned_to,parent_id',
            'logs',
            'assignee:id,name,email',
            'creator:id,name,email',
            'parent:id,title'
        ]);

        // Return detailed task as JSON
        return response()->json($task);
    }

    /**
     * Update an existing task (title, description, status, assigned_to).
     */
    public function update(Request $request, Task $task)
    {
        $user = Auth::user();

        // Validate request input
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'assigned_to' => 'sometimes|required|integer|exists:users,id',
            'status'      => 'sometimes|required|in:TODO,IN_PROGRESS,READY_FOR_TEST,PO_REVIEW,DONE,REJECTED',
        ]);

        $updates = [];

        // Product Owner can update core fields
        if ($user->role === 'product_owner') {
            if (isset($validated['title']) && $task->title !== $validated['title']) {
                $updates[] = ['field_changed' => 'title', 'old_value' => $task->title, 'new_value' => $validated['title']];
                $task->title = $validated['title'];
            }

            if (isset($validated['description']) && $task->description !== $validated['description']) {
                $updates[] = ['field_changed' => 'description', 'old_value' => $task->description, 'new_value' => $validated['description']];
                $task->description = $validated['description'];
            }

            if (isset($validated['assigned_to']) && $task->assigned_to !== $validated['assigned_to']) {
                $updates[] = [
                    'field_changed' => 'assigned_to',
                    'old_value'     => $task->assigned_to,
                    'new_value'     => $validated['assigned_to'],
                ];
                $task->assigned_to = $validated['assigned_to'];
            }
        }

        // Handle status changes by role permissions
        if (isset($validated['status']) && $task->status !== $validated['status']) {
            $newStatus = $validated['status'];

            $allowed = match ($user->role) {
                'developer' => $task->assigned_to === $user->id &&
                    in_array([$task->status, $newStatus], [['TODO', 'IN_PROGRESS'], ['IN_PROGRESS', 'READY_FOR_TEST']]),
                'tester' => $task->assigned_to === $user->id &&
                    $task->status === 'READY_FOR_TEST' && $newStatus === 'PO_REVIEW',
                'product_owner' => in_array($newStatus, ['DONE', 'IN_PROGRESS', 'REJECTED']),
                default => false,
            };

            if (! $allowed) {
                // Return forbidden if role cannot transition to requested status
                return response()->json(['message' => 'Invalid status transition for your role.'], 403);
            }

            $updates[] = ['field_changed' => 'status', 'old_value' => $task->status, 'new_value' => $newStatus];
            $task->status = $newStatus;

            // Trigger auto-assignment logic based on status
            $this->handleAutoAssignment($task, $newStatus);
        }

        // Notify assigned user if assignment changed
        if ($task->isDirty('assigned_to') && $task->assigned_to) {
            $task->assignee?->notify(new TaskAssigned($task));
        }

        // Save task changes
        $task->save();

        // Log each change in the task_logs table
        foreach ($updates as $update) {
            TaskLog::create([
                'task_id'       => $task->id,
                'changed_by'    => $user->id,
                'field_changed' => $update['field_changed'],
                'old_value'     => $update['old_value'],
                'new_value'     => $update['new_value'],
            ]);
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task'    => $task,
        ]);
    }

    /**
     * Automatically assign the task based on its status.
     */
    protected function handleAutoAssignment(Task $task, string $newStatus)
    {
        // When moving to PO_REVIEW or REJECTED, assign to Product Owner
        if (in_array($newStatus, ['PO_REVIEW', 'REJECTED'])) {
            $task->assigned_to = $task->created_by;
        }

        // When moving to READY_FOR_TEST, assign to tester with fewest tasks
        if ($newStatus === 'READY_FOR_TEST') {
            $tester = User::where('role', 'tester')
                ->withCount('tasksAssigned')
                ->orderBy('tasks_assigned_count', 'asc')
                ->first();

            if ($tester) {
                $task->assigned_to = $tester->id;
            }
        }

        // When moving to IN_PROGRESS or DONE, reassign to previous developer
        if (in_array($newStatus, ['IN_PROGRESS', 'DONE'])) {
            $lastDev = TaskLog::where('task_id', $task->id)
                ->where('field_changed', 'status')
                ->whereIn('new_value', ['IN_PROGRESS', 'READY_FOR_TEST'])
                ->latest()
                ->first();

            if ($lastDev) {
                $task->assigned_to = $lastDev->changed_by;
            }
        }
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        // Delete task from the database
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
