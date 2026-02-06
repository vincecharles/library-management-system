@extends('layouts.app')

@section('title', 'Backup & Restore - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-database me-2"></i>Backup & Restore</h1>
</div>

{{-- Backup & Restore Cards --}}
<div class="row mb-4">
    {{-- Create Backup --}}
    <div class="col-md-6 mb-3">
        <div class="card h-100" style="border-left: 4px solid #198754;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon me-3" style="background-color: #198754;">
                        <i class="fas fa-download"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Create Backup</h5>
                </div>
                <div class="info-card info-green mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Create a full backup of your library database. This includes all books, users, transactions, and settings.
                </div>
                @if(count($backups) > 0)
                <p class="text-muted mb-3">
                    <i class="fas fa-clock me-1"></i>
                    Last backup: <strong>{{ $backups[0]['date'] }}</strong>
                </p>
                @else
                <p class="text-muted mb-3">
                    <i class="fas fa-clock me-1"></i>
                    Last backup: <strong>No backups yet</strong>
                </p>
                @endif
                <form action="{{ route('backup.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-download me-2"></i> Backup Now
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Restore Database --}}
    <div class="col-md-6 mb-3">
        <div class="card h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon me-3" style="background-color: #ffc107;">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Restore Database</h5>
                </div>
                <div class="info-card info-yellow mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Warning:</strong> This will replace all current data. Make sure to create a backup before restoring.
                </div>
                <form action="{{ route('backup.restore') }}" method="POST" id="restoreForm">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_file" class="form-label">Select Backup to Restore</label>
                        <select name="backup_file" id="backup_file" class="form-select" required>
                            <option value="">Choose a backup...</option>
                            @foreach($backups as $backup)
                                <option value="{{ $backup['path'] }}">{{ $backup['name'] }} ({{ $backup['date'] }} - {{ $backup['size'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100" onclick="return confirm('Are you sure? This will replace all current data.')">
                        <i class="fas fa-upload me-2"></i> Restore
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Backup History --}}
<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>Backup History
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Filename</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                    <tr>
                        <td>{{ $backup['date'] }}</td>
                        <td>
                            <i class="fas fa-file-archive text-muted me-1"></i>
                            {{ $backup['name'] }}
                        </td>
                        <td>{{ $backup['size'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No backups found. Create your first backup using the button above.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
