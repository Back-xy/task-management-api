<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * List all users (PO only)
     */
    public function index()
    {
        if (Auth::user()->role !== 'product_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(User::all());
    }

    /**
     * Create a user (PO only)
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'product_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:product_owner,developer,tester',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201);
    }

    /**
     * Update a user (PO only)
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'product_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,$user->id",
            'password' => 'nullable|string|min:6',
            'role'     => 'sometimes|in:product_owner,developer,tester',
        ]);

        if ($request->filled('name'))     $user->name = $request->name;
        if ($request->filled('email'))    $user->email = $request->email;
        if ($request->filled('role'))     $user->role = $request->role;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user,
        ]);
    }

    /**
     * Delete a user (PO only)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'product_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
