@extends('layouts.app')

@section('title', 'Librarian Dashboard - AklatBayon')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Librarian Dashboard</h1>
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
                        <div class="stat-number">{{ number_format($todayIssues) }}</div>
                        <div class="stat-label">Today's Issues</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#28a745">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($todayReturns) }}</div>
                        <div class="stat-label">Today's Returns</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#ffc107">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">{{ number_format($dueToday) }}</div>
                        <div class="stat-label">Due Today</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:#dc3545">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stat-number">P{{ number_format($finesCollected, 2) }}</div>
                        <div class="stat-label">Fines Collected</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Search -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-2"></i>Quick Search
        </div>
        <div class="card-body">
            <form action="{{ route('books.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by book title, ISBN, student ID, or student name..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Due Today -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-check me-2"></i>Books Due Today
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Book Title</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booksDueToday as $record)
                            <tr>
                                <td>{{ $record->student->student_no }}</td>
                                <td>{{ $record->student->full_name }}</td>
                                <td>{{ $record->bookCopy->book->title ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('circulation.return') }}" class="btn btn-sm btn-primary btn-action">
                                        <i class="fas fa-undo me-1"></i> Return
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No books due today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
