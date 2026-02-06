@extends('layouts.app')

@section('title', 'Audit Logs - Library Management System')

@push('styles')
<style>
    .badge-return {
        background-color: #6f42c1;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-list me-2"></i>Audit Logs</h1>
    <div>
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-csv me-1"></i> CSV
        </a>
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('audit-logs.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}" placeholder="Search logs...">
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control" id="date_from" name="date_from"
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control" id="date_to" name="date_to"
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label for="module" class="form-label">Module</label>
                <select class="form-select" id="module" name="module">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                    <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                        {{ $module }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="action" class="form-label">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Audit Logs Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-list me-2"></i>Activity Logs</span>
        <span class="text-muted small">Showing {{ $logs->count() }} of {{ $logs->total() }} entries</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $log->user->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $actionBadges = [
                                    'login'  => 'bg-success',
                                    'logout' => 'bg-secondary',
                                    'create' => 'bg-warning text-dark',
                                    'update' => 'bg-info',
                                    'delete' => 'bg-danger',
                                    'issue'  => 'bg-primary',
                                    'return' => 'badge-return',
                                ];
                                $badgeClass = $actionBadges[strtolower($log->action)] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td>{{ $log->module }}</td>
                        <td>{{ Str::limit($log->details ?? '-', 60) }}</td>
                        <td><code>{{ $log->ip_address ?? 'N/A' }}</code></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No audit logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
