@extends('layouts.app')

@section('title', 'Student Profile - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-graduate me-2"></i>Student Profile</h1>
    <a href="{{ route('students.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Students
    </a>
</div>

<!-- Student Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="me-4">
                <i class="fas fa-user-circle text-muted" style="font-size: 80px;"></i>
            </div>
            <div>
                <h3 class="mb-1">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</h3>
                <p class="text-muted mb-2">
                    <i class="fas fa-id-card me-1"></i> {{ $student->student_no }}
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    @if($student->status == 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($student->status == 'inactive')
                        <span class="badge bg-secondary">Inactive</span>
                    @elseif($student->status == 'graduated')
                        <span class="badge bg-info">Graduated</span>
                    @elseif($student->status == 'transferred')
                        <span class="badge bg-warning text-dark">Transferred</span>
                    @endif
                    <span class="badge bg-primary">{{ $student->gradeLevel->name ?? 'N/A' }}</span>
                    <span class="badge bg-dark">{{ $student->section->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-1"></i> Personal Information
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="fw-bold text-muted" style="width: 40%;">LRN</td>
                        <td>{{ $student->lrn ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">School Year</td>
                        <td>{{ $student->school_year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Contact Number</td>
                        <td>{{ $student->contact_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Email Address</td>
                        <td>{{ $student->email ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td class="fw-bold text-muted" style="width: 40%;">Address</td>
                        <td>{{ $student->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Guardian Name</td>
                        <td>{{ $student->guardian_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Guardian Contact</td>
                        <td>{{ $student->guardian_contact ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Library Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background-color: var(--primary-blue);">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-number">{{ $activeIssues->count() }} / {{ $student->max_books_allowed }}</div>
            <div class="stat-label">Books Borrowed (Current)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background-color: var(--success);">
                    <i class="fas fa-book-reader"></i>
                </div>
            </div>
            <div class="stat-number">{{ $transactions->total() }}</div>
            <div class="stat-label">Total Borrowed (All Time)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background-color: var(--danger);">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="stat-number">&#8369;{{ number_format($fines->where('status', 'unpaid')->sum('amount'), 2) }}</div>
            <div class="stat-label">Outstanding Fines</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon" style="background-color: var(--info);">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-number">&#8369;{{ number_format($fines->where('status', 'paid')->sum('amount'), 2) }}</div>
            <div class="stat-label">Total Fines Paid</div>
        </div>
    </div>
</div>

<!-- Current Borrowings -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-book-open me-1"></i> Current Borrowings
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeIssues as $transaction)
                    <tr>
                        <td>{{ $transaction->bookCopy->book->title ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->issue_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->due_date)->format('M d, Y') }}</td>
                        <td>
                            @if($transaction->due_date < now() && !$transaction->return_date)
                                <span class="badge bg-danger">Overdue</span>
                            @elseif(!$transaction->return_date)
                                <span class="badge bg-warning text-dark">Borrowed</span>
                            @else
                                <span class="badge bg-success">Returned</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-book fa-2x mb-2 d-block"></i>
                            No current borrowings.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
