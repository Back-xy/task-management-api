<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Allow all roles to view the list of tasks.
     */
    public function viewAny(User $user)
    {
        return in_array($user->role, ['product_owner', 'developer', 'tester']);
    }

    /**
     * Allow all users to view individual tasks.
     */
    public function view(User $user, Task $task)
    {
        return true;
    }

    /**
     * Allow only product owners to create tasks.
     */
    public function create(User $user)
    {
        return $user->role === 'product_owner';
    }

    /**
     * Allow all roles to attempt task updates.
     * (Detailed role-based logic is handled inside the controller.)
     */
    public function update(User $user, Task $task)
    {
        return true;
    }

    /**
     * Allow only product owners to delete tasks.
     */
    public function delete(User $user, Task $task)
    {
        return $user->role === 'product_owner';
    }
}
