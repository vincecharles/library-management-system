@extends('layouts.app')

@section('title', 'Return Book - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-undo me-2"></i>Return Book</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-book me-2"></i>Return Book Form
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

        <form method="POST" action="{{ route('circulation.return.store') }}" id="returnForm">
            @csrf

            {{-- ============================== --}}
            {{-- Book Scan Section --}}
            {{-- ============================== --}}
            <h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
                <i class="fas fa-barcode me-2"></i>Scan Book
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
            {{-- Book Condition Section --}}
            {{-- ============================== --}}
            <h5 class="mb-3" style="color: var(--primary-navy); font-weight: 700;">
                <i class="fas fa-clipboard-check me-2"></i>Book Condition
            </h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="condition_on_return" class="form-label">Condition upon Return</label>
                    <select class="form-select @error('condition_on_return') is-invalid @enderror"
                            id="condition_on_return" name="condition_on_return">
                        <option value="Good" {{ old('condition_on_return', 'Good') === 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ old('condition_on_return') === 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Damaged" {{ old('condition_on_return') === 'Damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="Lost" {{ old('condition_on_return') === 'Lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                    @error('condition_on_return')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror"
                              id="notes" name="notes" rows="3"
                              placeholder="Add any notes about the book condition or return (optional)">{{ old('notes') }}</textarea>
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
                    <i class="fas fa-check-circle me-2"></i>Process Return
                </button>
                <a href="{{ route('circulation.return') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
