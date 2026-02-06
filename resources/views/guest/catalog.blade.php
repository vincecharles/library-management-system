<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Catalog - Book Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
        }
        .guest-header {
            background-color: #6c757d;
            padding: 16px 0;
        }
        .guest-header h4 {
            color: #ffffff;
            margin: 0;
            font-weight: 700;
        }
        .search-section .form-control {
            font-size: 16px;
            padding: 12px 20px;
            border-radius: 8px 0 0 8px;
        }
        .search-section .btn {
            padding: 12px 24px;
            border-radius: 0 8px 8px 0;
        }
        .category-tag {
            display: inline-block;
            padding: 6px 16px;
            margin: 4px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .category-tag:hover {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }
        .category-tag.active {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }
        .book-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
            height: 100%;
        }
        .book-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .book-cover-placeholder {
            background-color: #e9ecef;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }
        .book-cover-placeholder i {
            font-size: 48px;
        }
        .availability-badge {
            font-size: 13px;
            font-weight: 600;
        }
    </style>
</head>
<body style="background:#ffffff">

    {{-- Header Bar --}}
    <header class="guest-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <h4>
                    <i class="fas fa-book-open me-2"></i>
                    {{ $libraryName ?? 'Library Management System' }}
                </h4>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white-50 small">Have an account?</span>
                    <a href="{{ route('login') }}" class="btn btn-danger">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="container mt-4">

        {{-- Search Bar --}}
        <div class="search-section mb-4">
            <form method="GET" action="{{ url()->current() }}">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search books by title, author, subject, ISBN...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Category Tags --}}
        @if(isset($categories) && count($categories) > 0)
        <div class="mb-4">
            <a href="{{ url()->current() }}" class="category-tag bg-light text-dark border {{ !request('category') ? 'active' : '' }}">
                All
            </a>
            @foreach($categories as $category)
            <a href="{{ url()->current() }}?category={{ $category->id }}{{ request('search') ? '&search='.request('search') : '' }}"
               class="category-tag bg-light text-dark border {{ request('category') == $category->id ? 'active' : '' }}">
                {{ $category->name }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Book Cards Grid --}}
        <div class="row">
            @forelse($books ?? [] as $book)
            <div class="col-md-4 mb-4">
                <div class="book-card">
                    {{-- Cover Placeholder --}}
                    <div class="book-cover-placeholder">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                 class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>

                    {{-- Book Info --}}
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">{{ $book->title }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-pen-fancy me-1"></i>
                            {{ $book->authors->pluck('name')->join(', ') ?: 'Unknown Author' }}
                        </p>

                        @if($book->category)
                        <span class="badge bg-light text-dark border mb-2">{{ $book->category->name ?? $book->category_name ?? '' }}</span>
                        @endif

                        {{-- Availability --}}
                        <div class="mt-2">
                            @php
                                $availableCopies = $book->available_copies ?? $book->quantity ?? 0;
                            @endphp
                            @if($availableCopies > 0)
                                <span class="availability-badge text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $availableCopies }} {{ Str::plural('copy', $availableCopies) }} available
                                </span>
                            @else
                                <span class="availability-badge text-danger">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Not available
                                </span>
                            @endif
                        </div>

                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-lock me-1"></i> Login to borrow
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-3x mb-3 d-block"></i>
                    <h5>No books found</h5>
                    <p>Try adjusting your search or filter criteria.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($books) && method_exists($books, 'hasPages') && $books->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            {{ $books->appends(request()->query())->links() }}
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center py-4 border-top mt-3">
            <p class="text-muted mb-0">
                <i class="fas fa-book me-1"></i>
                Total Books in Library: <strong>{{ isset($books) && method_exists($books, 'total') ? number_format($books->total()) : (isset($books) ? number_format(count($books)) : 0) }}</strong>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
