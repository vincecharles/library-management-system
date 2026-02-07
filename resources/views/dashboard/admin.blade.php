@extends('layouts.app')

@section('title', 'Admin Dashboard - AklatBayon')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#0f3460">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($totalBooks) }}</div>
                        <div class="stat-label">Total Books</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#28a745">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($activeStudents) }}</div>
                        <div class="stat-label">Active Students</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#ffc107">
                        <i class="fas fa-hand-holding"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($booksIssued) }}</div>
                        <div class="stat-label">Books Issued</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#dc3545">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($overdueBooks) }}</div>
                        <div class="stat-label">Overdue Books</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-clock me-2"></i>Recent Activity
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td>{{ $activity->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $activity->user->name }}</td>
                                <td>{{ $activity->action }}</td>
                                <td>{{ $activity->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No recent activity found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h5 class="mb-3" style="font-weight:700; color:#1a1a2e;">Quick Actions</h5>
    <div class="row g-4">
        <div class="col-md-3">
            <a href="{{ route('books.create') }}" class="quick-action">
                <i class="fas fa-plus-circle" style="color:#0f3460;"></i>
                <div class="action-title">Add Book</div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('students.create') }}" class="quick-action">
                <i class="fas fa-user-plus" style="color:#28a745;"></i>
                <div class="action-title">Add Student</div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('circulation.issue') }}" class="quick-action">
                <i class="fas fa-hand-holding" style="color:#ffc107;"></i>
                <div class="action-title">Issue Book</div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('circulation.return') }}" class="quick-action">
                <i class="fas fa-undo" style="color:#dc3545;"></i>
                <div class="action-title">Return Book</div>
            </a>
        </div>
    </div>
@endsection
