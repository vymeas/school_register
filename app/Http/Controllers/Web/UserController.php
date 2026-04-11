<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_deleted', false);

        // Don't show super_admin to non-super_admin users
        if (auth()->user()->role !== 'super_admin') {
            $query->where('role', '!=', 'super_admin');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        return view('users.index', [
            'users' => $query->orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $allowedRoles = 'admin,accountant';
        if (auth()->user()->role === 'super_admin') {
            $allowedRoles .= ',super_admin';
        }

        $data = $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|max:255',
            'role' => "required|in:{$allowedRoles}",
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
}
