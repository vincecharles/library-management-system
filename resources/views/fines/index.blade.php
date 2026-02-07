@extends('layouts.app')

@section('title', 'Fine Management - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-money-bill-wave me-2"></i>Fine Management</h1>
</div>

{{-- ============================== --}}
{{-- Statistics Cards --}}
{{-- ============================== --}}
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background-color: #dc3545;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <div class="stat-number">P{{ number_format($totalUnpaid ?? 0, 2) }}</div>
                    <div class="stat-label">Total Outstanding</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background-color: #28a745;">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="stat-number">P{{ number_format($totalCollected ?? 0, 2) }}</div>
                    <div class="stat-label">Total Collected</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- Search Bar --}}
{{-- ============================== --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('fines.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Student with Fines</label>
                <input type="text" class="form-control" id="search" name="search"
                       placeholder="Enter student name, ID, or book title..."
                       value="{{ request('search', '') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100" style="background: #0f3460; border-color: #0f3460;">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ============================== --}}
{{-- Pending Fines List --}}
{{-- ============================== --}}
<h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
    <i class="fas fa-list me-2"></i>Pending Fines
</h5>

@forelse($fines as $fine)
<div class="card mb-3">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-1">{{ $fine->student->first_name ?? '' }} {{ $fine->student->last_name ?? '' }}</h6>
            <small class="text-muted">
                {{ $fine->transaction->bookCopy->book->title ?? 'N/A' }} -
                <span class="badge bg-secondary">{{ ucfirst($fine->fine_type ?? 'overdue') }}</span>
            </small>
            <div class="mt-1"><strong class="text-danger">P{{ number_format($fine->amount ?? 0, 2) }}</strong></div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success collect-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#collectModal"
                    data-fine-id="{{ $fine->id }}"
                    data-fine-amount="{{ number_format($fine->amount ?? 0, 2) }}"
                    data-student-name="{{ ($fine->student->first_name ?? '') . ' ' . ($fine->student->last_name ?? '') }}"
                    data-book-title="{{ $fine->transaction->bookCopy->book->title ?? 'N/A' }}">
                <i class="fas fa-hand-holding-usd me-1"></i> Collect
            </button>
            <form method="POST" action="{{ route('fines.waive', $fine->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning"
                        onclick="return confirm('Are you sure you want to waive this fine?')">
                    <i class="fas fa-times-circle me-1"></i> Waive
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <p class="fs-5 mb-0">No pending fines found.</p>
    </div>
</div>
@endforelse

{{-- ============================== --}}
{{-- Pagination --}}
{{-- ============================== --}}
@if(isset($fines) && method_exists($fines, 'hasPages') && $fines->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $fines->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endif

{{-- ============================== --}}
{{-- Fine Collection Modal --}}
{{-- ============================== --}}
<div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="collectForm" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="collectModalLabel">
                        <i class="fas fa-cash-register me-2"></i>Collect Fine
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="info-card info-blue mb-3">
                        <p class="mb-1"><strong>Student:</strong> <span id="modalStudentName"></span></p>
                        <p class="mb-0"><strong>Book:</strong> <span id="modalBookTitle"></span></p>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">P</span>
                            <input type="number" class="form-control" id="amount" name="amount"
                                   step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash" checked>
                                <label class="form-check-label" for="payCash">
                                    <i class="fas fa-money-bill-wave me-1"></i> Cash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payGcash" value="gcash">
                                <label class="form-check-label" for="payGcash">
                                    <i class="fas fa-mobile-alt me-1"></i> GCash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payOther" value="other">
                                <label class="form-check-label" for="payOther">
                                    <i class="fas fa-credit-card me-1"></i> Other
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="3"
                                  placeholder="Add any payment notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Populate the collection modal with fine data
    document.querySelectorAll('.collect-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var fineId = this.getAttribute('data-fine-id');
            var fineAmount = this.getAttribute('data-fine-amount');
            var studentName = this.getAttribute('data-student-name');
            var bookTitle = this.getAttribute('data-book-title');

            document.getElementById('collectForm').action = '/fines/' + fineId + '/collect';
            document.getElementById('amount').value = fineAmount;
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('modalBookTitle').textContent = bookTitle;
        });
    });
</script>
@endpush
