<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles with their permission counts.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the permission management form for a role.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('group');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.permissions', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the permissions for a role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];

        $role->permissions()->sync($permissionIds);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Role Permissions',
            'module'     => 'Role Management',
            'details'    => "Updated permissions for role '{$role->name}'. Assigned " . count($permissionIds) . " permissions.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Permissions for '{$role->name}' have been updated successfully.");
    }
}
