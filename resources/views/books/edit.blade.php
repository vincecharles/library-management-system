@extends('layouts.app')

@section('title', 'Edit Book - AklatBayon')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i>Edit Book</h1>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Catalog
    </a>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-book me-2"></i>Book Information
    </div>
    <div class="card-body">
        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" name="isbn" id="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn', $book->isbn) }}" placeholder="Enter ISBN">
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $book->title) }}" placeholder="Enter book title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="publication_year" class="form-label">Publication Year</label>
                        <input type="number" name="publication_year" id="publication_year" class="form-control @error('publication_year') is-invalid @enderror" value="{{ old('publication_year', $book->publication_year) }}" placeholder="e.g. 2024" min="1000" max="{{ date('Y') }}">
                        @error('publication_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="copies" class="form-label">Copies <span class="text-danger">*</span></label>
                        <input type="number" name="copies" id="copies" class="form-control @error('copies') is-invalid @enderror" value="{{ old('copies', $book->total_copies) }}" min="1" required>
                        @error('copies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="shelf_location" class="form-label">Shelf Location</label>
                        <input type="text" name="shelf_location" id="shelf_location" class="form-control @error('shelf_location') is-invalid @enderror" value="{{ old('shelf_location', $book->shelf_location) }}" placeholder="e.g. A-01-03">
                        @error('shelf_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="authors" class="form-label">Author(s) <span class="text-danger">*</span></label>
                        <select name="authors[]" id="authors" class="form-select @error('authors') is-invalid @enderror" multiple required size="5">
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ in_array($author->id, old('authors', $book->authors->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple authors</small>
                        @error('authors')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="publishers" class="form-label">Publisher(s)</label>
                        <select name="publishers[]" id="publishers" class="form-select @error('publishers') is-invalid @enderror" multiple size="4">
                            @foreach($publishers as $publisher)
                                <option value="{{ $publisher->id }}" {{ in_array($publisher->id, old('publishers', $book->publishers->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $publisher->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple publishers</small>
                        @error('publishers')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter book description">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Cover Image</label>
                        @if($book->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Current Cover" class="img-thumbnail" style="max-height: 120px;">
                                <small class="d-block text-muted mt-1">Current cover image. Upload a new one to replace it.</small>
                            </div>
                        @endif
                        <input type="file" name="cover_image" id="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Save Book
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
