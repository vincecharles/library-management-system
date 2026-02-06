@extends('layouts.app')

@section('title', 'User Management - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-users-cog me-2"></i>User Management</h1>
    <a href="{{ route('users.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Add User
    </a>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('users.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name, username, or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role && $user->role->name == 'Administrator')
                                <span class="badge bg-dark">Administrator</span>
                            @elseif($user->role && $user->role->name == 'Librarian')
                                <span class="badge bg-danger">Librarian</span>
                            @elseif($user->role && $user->role->name == 'Assistant Librarian')
                                <span class="badge bg-success">Assistant Librarian</span>
                            @else
                                <span class="badge bg-secondary">{{ $user->role->name ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge badge-active bg-success">Active</span>
                            @elseif($user->status == 'inactive')
                                <span class="badge badge-inactive bg-secondary">Inactive</span>
                            @elseif($user->status == 'locked')
                                <span class="badge bg-warning text-dark">Locked</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-3">
    {{ $users->links() }}
</div>
@endsection
