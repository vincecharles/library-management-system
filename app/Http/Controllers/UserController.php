<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create User',
            'module'     => 'User Management',
            'details'    => "Created user account for {$user->name} (Username: {$user->username}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' has been created successfully.");
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'status'   => 'required|in:active,inactive,locked',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Reset failed login attempts if unlocking the account
        if ($user->status === 'locked' && $validated['status'] === 'active') {
            $validated['failed_login_attempts'] = 0;
        }

        $user->update($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update User',
            'module'     => 'User Management',
            'details'    => "Updated user account for {$user->name} (Username: {$user->username}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' has been updated successfully.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete User',
            'module'     => 'User Management',
            'details'    => "Deleted user account for {$userName}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }
}
