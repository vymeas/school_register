<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('is_deleted', false);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('full_name')->paginate($request->per_page ?? 15);

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:super_admin,admin,accountant,registrar,teacher',
            'status' => 'nullable|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::where('is_deleted', false)->findOrFail($id);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::where('is_deleted', false)->findOrFail($id);

        $data = $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id . '|max:255',
            'password' => 'nullable|string|min:6',
            'full_name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:super_admin,admin,accountant,registrar,teacher',
            'status' => 'nullable|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::where('is_deleted', false)->findOrFail($id);

        // Soft delete by setting is_deleted flag
        $user->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }
}
