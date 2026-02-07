@extends('layouts.app')

@section('title', $book->title . ' - AklatBayon')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-book me-2"></i>Book Details</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Catalog
        </a>
        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('circulation.issue') }}" class="btn btn-success">
            <i class="fas fa-hand-holding me-1"></i> Issue Book
        </a>
    </div>
</div>

<!-- Book Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <!-- Left Column: Cover Image -->
            <div class="col-md-4 text-center">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="img-fluid rounded shadow" style="max-height: 400px;">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded" style="height: 350px; background-color: #e9ecef;">
                        <div class="text-center text-muted">
                            <i class="fas fa-book fa-5x mb-3"></i>
                            <p>No Cover Image</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Book Details -->
            <div class="col-md-8">
                <h3 class="fw-bold mb-2">{{ $book->title }}</h3>
                <p class="text-muted mb-3">
                    <i class="fas fa-pen-fancy me-1"></i> {{ $book->authors->pluck('name')->join(', ') }}
                </p>

                <!-- Status & Category Badges -->
                <div class="mb-3">
                    @if($book->available_copies > 0)
                        <span class="badge bg-success fs-6 me-2">Available</span>
                    @else
                        <span class="badge bg-danger fs-6 me-2">Unavailable</span>
                    @endif
                    <span class="badge bg-info fs-6">{{ $book->category->name ?? 'Uncategorized' }}</span>
                </div>

                <!-- Info Grid -->
                <table class="table table-borderless mb-0" style="max-width: 500px;">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 160px;">ISBN</td>
                            <td>{{ $book->isbn ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Publisher</td>
                            <td>{{ $book->publishers->pluck('name')->join(', ') ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Publication Year</td>
                            <td>{{ $book->publication_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Copies</td>
                            <td>{{ $book->total_copies }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Available Copies</td>
                            <td>
                                @if($book->available_copies > 0)
                                    <span class="text-success fw-bold">{{ $book->available_copies }}</span>
                                @else
                                    <span class="text-danger fw-bold">0</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Shelf Location</td>
                            <td>{{ $book->shelf_location ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Date Added</td>
                            <td>{{ $book->created_at->format('M d, Y') }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Description -->
                @if($book->description)
                    <hr>
                    <h6 class="fw-bold text-muted">Description</h6>
                    <p class="mb-0">{{ $book->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Book Copies Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-copy me-2"></i>Book Copies
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Accession No</th>
                        <th>Barcode</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($book->copies as $copy)
                        <tr>
                            <td>{{ $copy->accession_no ?? 'N/A' }}</td>
                            <td>{{ $copy->barcode ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $conditionBadges = [
                                        'new' => 'bg-primary',
                                        'good' => 'bg-success',
                                        'fair' => 'bg-warning',
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
                                        'borrowed' => 'bg-warning',
                                        'reserved' => 'bg-info',
                                        'lost' => 'bg-dark',
                                    ];
                                    $statusClass = $statusBadges[$copy->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($copy->status) }}</span>
                            </td>
                            <td>{{ $copy->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-copy fa-2x mb-2 d-block"></i>
                                No copies found for this book.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
