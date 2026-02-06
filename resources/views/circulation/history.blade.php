@extends('layouts.app')

@section('title', 'Borrowing History - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-history me-2"></i>Borrowing History</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('circulation.history', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success">
            <i class="fas fa-file-export me-1"></i> Export
        </a>
        <button type="button" class="btn btn-info text-white" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>
</div>

{{-- ============================== --}}
{{-- Filter Bar --}}
{{-- ============================== --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('circulation.history') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       placeholder="Student name, book title, ID..."
                       value="{{ request('search', '') }}">
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="{{ request('start_date', '') }}">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="{{ request('end_date', '') }}">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('circulation.history') }}" class="btn btn-secondary w-100" title="Clear Filters">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ============================== --}}
{{-- Data Table --}}
{{-- ============================== --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Trans ID</th>
                        <th>Student</th>
                        <th>Book Title</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td><strong>#{{ $transaction->id }}</strong></td>
                        <td>
                            @if($transaction->student)
                                <strong>{{ $transaction->student->student_no }}</strong><br>
                                <small>{{ $transaction->student->full_name }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $transaction->bookCopy->book->title ?? 'N/A' }}</td>
                        <td>{{ $transaction->issue_date ? \Carbon\Carbon::parse($transaction->issue_date)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $transaction->due_date ? \Carbon\Carbon::parse($transaction->due_date)->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if($transaction->return_date)
                                {{ \Carbon\Carbon::parse($transaction->return_date)->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                $status = strtolower($transaction->status ?? 'borrowed');
                            @endphp
                            @if($status === 'borrowed')
                                <span class="badge bg-warning text-dark">Borrowed</span>
                            @elseif($status === 'returned')
                                <span class="badge bg-success">Returned</span>
                            @elseif($status === 'overdue')
                                <span class="badge bg-danger">Overdue</span>
                            @elseif($status === 'lost')
                                <span class="badge bg-dark">Lost</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No borrowing records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- Pagination --}}
{{-- ============================== --}}
@if(isset($transactions) && $transactions->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $transactions->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endif
@endsection
