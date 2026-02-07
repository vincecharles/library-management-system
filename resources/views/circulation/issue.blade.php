@extends('layouts.app')

@section('title', 'Issue Book - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-hand-holding me-2"></i>Issue Book</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-book-reader me-2"></i>Issue Book Form
    </div>
    <div class="card-body p-4">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('circulation.issue.store') }}" id="issueForm">
            @csrf

            {{-- ============================== --}}
            {{-- Student Section --}}
            {{-- ============================== --}}
            <h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
                <i class="fas fa-user-graduate me-2"></i>Student Information
            </h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_no" class="form-label">Student ID / Library Card Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('student_no') is-invalid @enderror"
                           id="student_no" name="student_no"
                           placeholder="Enter Student ID or Library Card Number"
                           value="{{ old('student_no') }}" required>
                    @error('student_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- ============================== --}}
            {{-- Book Section --}}
            {{-- ============================== --}}
            <h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
                <i class="fas fa-book me-2"></i>Book Information
            </h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="barcode" class="form-label">Book Barcode / Accession Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                           id="barcode" name="barcode"
                           placeholder="Enter barcode or accession number"
                           value="{{ old('barcode') }}" required>
                    @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- ============================== --}}
            {{-- Loan Details Section --}}
            {{-- ============================== --}}
            <h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
                <i class="fas fa-calendar-alt me-2"></i>Loan Details
            </h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                           id="due_date" name="due_date"
                           value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Default loan period is <strong>7 days</strong>. Leave blank to use system default.
                    </small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="condition_on_borrow" class="form-label">Condition on Borrow</label>
                    <select class="form-select @error('condition_on_borrow') is-invalid @enderror"
                            id="condition_on_borrow" name="condition_on_borrow">
                        <option value="Good" {{ old('condition_on_borrow', 'Good') === 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ old('condition_on_borrow') === 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Poor" {{ old('condition_on_borrow') === 'Poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition_on_borrow')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror"
                              id="notes" name="notes" rows="3"
                              placeholder="Add any notes about this transaction (optional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- ============================== --}}
            {{-- Submit Buttons --}}
            {{-- ============================== --}}
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Issue Book
                </button>
                <a href="{{ route('circulation.issue') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
