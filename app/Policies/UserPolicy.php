<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Allow only product owners to view the list of users.
     */
    public function viewAny(User $user)
    {
        return $user->role === 'product_owner';
    }

    /**
     * Allow only product owners to create new users.
     */
    public function create(User $user)
    {
        return $user->role === 'product_owner';
    }

    /**
     * Allow only product owners to update user profiles.
     */
    public function update(User $user, User $model)
    {
        return $user->role === 'product_owner';
    }

    /**
     * Allow only product owners to delete users.
     */
    public function delete(User $user, User $model)
    {
        return $user->role === 'product_owner';
    }
}
