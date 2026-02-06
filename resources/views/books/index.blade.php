@extends('layouts.app')

@section('title', 'Book Catalog - Library Management System')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-book me-2"></i>Book Catalog</h1>
    <a href="{{ route('books.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Add Book
    </a>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('books.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, ISBN..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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

<!-- Books Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author(s)</th>
                        <th>Category</th>
                        <th>Copies</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->isbn }}</td>
                            <td><strong>{{ $book->title }}</strong></td>
                            <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                            <td>{{ $book->category->name ?? 'N/A' }}</td>
                            <td>{{ $book->total_copies }}</td>
                            <td>
                                @if($book->available_copies > 0)
                                    <span class="text-success fw-bold">{{ $book->available_copies }}</span>
                                @else
                                    <span class="text-danger fw-bold">0</span>
                                @endif
                            </td>
                            <td>
                                @if($book->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-action" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-action" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-book-open fa-2x mb-2 d-block"></i>
                                No books found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $books->links() }}
</div>
@endsection
