<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Return a list of all users.
     */
    public function index()
    {
        // Fetch and return all users as JSON
        return response()->json(User::all());
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:product_owner,developer,tester',
        ]);

        // Create the user with the validated data
        $user = User::create($validated);

        // Return success message and user data as JSON
        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201); // 201 Created
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        // Validate incoming update fields
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:6',
            'role'     => 'sometimes|in:product_owner,developer,tester',
        ]);

        // Apply changes to user fields if present
        if (isset($validated['name']))      $user->name = $validated['name'];
        if (isset($validated['email']))     $user->email = $validated['email'];
        if (isset($validated['role']))      $user->role = $validated['role'];
        if (!empty($validated['password'])) $user->password = Hash::make($validated['password']);

        // Save updated user
        $user->save();

        // Return success message and updated user as JSON
        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user,
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Delete the user
        $user->delete();

        // Return success message as JSON
        return response()->json(['message' => 'User deleted successfully']);
    }
}
