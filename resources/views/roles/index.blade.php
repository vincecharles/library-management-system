@extends('layouts.app')

@section('title', 'Role Management - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-shield me-2"></i>Role Management</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-1"></i> All Roles & Permissions
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="fw-semibold">{{ $role->name }}</span>
                            @if($role->name === 'Administrator')
                                <span class="badge bg-dark ms-1">Super</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $role->description }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $role->users()->count() }}</span>
                        </td>
                        <td>
                            @if($role->name === 'Administrator')
                                <span class="badge bg-success">All Permissions</span>
                            @else
                                <span class="badge bg-info">{{ $role->permissions_count }} assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($role->name !== 'Administrator')
                                <a href="{{ route('roles.permissions.edit', $role) }}" class="btn btn-sm btn-outline-primary btn-action">
                                    <i class="fas fa-sliders-h me-1"></i> Manage Permissions
                                </a>
                            @else
                                <span class="text-muted small">Full access (all permissions)</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="info-card info-blue">
        <i class="fas fa-info-circle me-2"></i>
        <strong>How it works:</strong> Roles define a group of permissions. Each user is assigned a role,
        and their access to system features is determined by the permissions of that role.
        Administrators automatically have all permissions.
    </div>
</div>
@endsection
