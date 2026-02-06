@extends('layouts.app')

@section('title', 'Categories Management - Library Management System')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-tags me-2"></i>Categories Management</h1>
    <button type="button" class="btn btn-success" onclick="openAddModal()">
        <i class="fas fa-plus me-1"></i> Add Category
    </button>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('categories.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by category name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Books Count</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ Str::limit($category->description, 50) ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $category->books_count }}</span></td>
                            <td>
                                @if($category->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-action" title="Edit"
                                    onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}', '{{ $category->status }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline delete-form">
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
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                No categories found.
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
    {{ $categories->links() }}
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#fff">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <input type="hidden" id="_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea name="description" id="categoryDescription" class="form-control" rows="3" placeholder="Enter description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select name="status" id="categoryStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('categoryForm').action = "{{ route('categories.store') }}";
        document.getElementById('_method').value = 'POST';
        document.getElementById('categoryName').value = '';
        document.getElementById('categoryDescription').value = '';
        document.getElementById('categoryStatus').value = 'active';
        categoryModal.show();
    }

    function openEditModal(id, name, description, status) {
        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('categoryForm').action = "{{ url('categories') }}/" + id;
        document.getElementById('_method').value = 'PUT';
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description;
        document.getElementById('categoryStatus').value = status;
        categoryModal.show();
    }
</script>
@endpush
