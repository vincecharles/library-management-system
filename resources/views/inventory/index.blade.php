@extends('layouts.app')

@section('title', 'Inventory Management - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-boxes-stacked me-2"></i>Inventory Management</h1>
</div>

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number">{{ number_format($summary['totalCopies']) }}</div>
                    <div class="stat-label">Total Copies</div>
                </div>
                <div class="stat-icon" style="background-color: #0d6efd;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number">{{ number_format($summary['availableCopies']) }}</div>
                    <div class="stat-label">Available</div>
                </div>
                <div class="stat-icon" style="background-color: #198754;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number">{{ number_format($summary['issuedCopies']) }}</div>
                    <div class="stat-label">Issued</div>
                </div>
                <div class="stat-icon" style="background-color: #fd7e14;">
                    <i class="fas fa-hand-holding"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number">{{ number_format($summary['damagedCopies'] + $summary['lostCopies']) }}</div>
                    <div class="stat-label">Damaged/Lost</div>
                </div>
                <div class="stat-icon" style="background-color: #dc3545;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number">{{ number_format($summary['totalTitles']) }}</div>
                    <div class="stat-label">Total Titles</div>
                </div>
                <div class="stat-icon" style="background-color: #6f42c1;">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('inventory.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by title, ISBN, accession no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Condition</label>
                <select name="condition" class="form-select">
                    <option value="">All Conditions</option>
                    <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>Good</option>
                    <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                    <option value="damaged" {{ request('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
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

{{-- Inventory Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-list me-2"></i>Book Copies Inventory</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Accession No</th>
                        <th>Barcode</th>
                        <th>Book Title</th>
                        <th>Category</th>
                        <th>Condition</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($copies as $copy)
                    <tr>
                        <td>{{ $copy->accession_no }}</td>
                        <td>{{ $copy->barcode }}</td>
                        <td>{{ $copy->book->title ?? 'N/A' }}</td>
                        <td>{{ $copy->book->category->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $conditionBadges = [
                                    'good' => 'bg-success',
                                    'fair' => 'bg-warning text-dark',
                                    'poor' => 'bg-danger',
                                    'damaged' => 'bg-danger',
                                    'lost' => 'bg-dark',
                                ];
                                $badgeClass = $conditionBadges[$copy->condition_status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($copy->condition_status) }}</span>
                        </td>
                        <td>
                            @php
                                $statusBadges = [
                                    'available' => 'bg-success',
                                    'issued' => 'bg-warning text-dark',
                                    'lost' => 'bg-dark',
                                ];
                                $statusClass = $statusBadges[$copy->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($copy->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No copies found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($copies->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $copies->appends(request()->query())->links() }}
</div>
@endif
@endsection
