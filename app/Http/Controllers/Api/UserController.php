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
        if ($request->has('trashed') && $request->trashed == 'true') {
            $query = User::query()->where('is_deleted', true);
        } else {
            $query = User::query()->where('is_deleted', false);
        }

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
        $user = User::query()->where('is_deleted', false)->findOrFail($id);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::query()->where('is_deleted', false)->findOrFail($id);

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
        $user = User::query()->where('is_deleted', false)->findOrFail($id);
        /** @var \App\Models\User $currentUser */
        $currentUser = request()->user();

        // Check permissions
        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }

        if ($currentUser->role !== 'super_admin') {
            if ($currentUser->role === 'admin' && $user->role !== 'accountant') {
                return response()->json(['message' => 'Admins can only delete accountants.'], 403);
            }
            if (!in_array($currentUser->role, ['super_admin', 'admin'])) {
                return response()->json(['message' => 'You do not have permission to delete users.'], 403);
            }
        }

        // Soft delete by setting is_deleted flag
        $user->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $user = User::query()->where('is_deleted', true)->findOrFail($id);
        /** @var \App\Models\User $currentUser */
        $currentUser = request()->user();

        // Check permissions
        if ($currentUser->role !== 'super_admin') {
            if ($currentUser->role === 'admin' && $user->role !== 'accountant') {
                return response()->json(['message' => 'Admins can only restore accountants.'], 403);
            }
            if (!in_array($currentUser->role, ['super_admin', 'admin'])) {
                return response()->json(['message' => 'You do not have permission to restore users.'], 403);
            }
        }

        $user->update(['is_deleted' => false]);

        return response()->json([
            'message' => 'User restored successfully.',
            'user' => $user,
        ]);
    }

    public function resetPassword(string $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        /** @var \App\Models\User $currentUser */
        $currentUser = request()->user();

        // Check permissions
        if ($currentUser->id !== $user->id && $currentUser->role !== 'super_admin') {
            if ($currentUser->role === 'admin' && $user->role !== 'accountant') {
                return response()->json(['message' => 'Admins can only reset passwords for accountants.'], 403);
            }
            if (!in_array($currentUser->role, ['super_admin', 'admin'])) {
                return response()->json(['message' => 'You do not have permission to reset passwords.'], 403);
            }
        }

        $user->update([
            'password' => '123123', // Mutator on User model should automatically hash this, if not we need Hash::make
        ]);

        return response()->json([
            'message' => 'User password has been reset to 123123.',
        ]);
    }
}
