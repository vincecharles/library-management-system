@extends('layouts.app')

@section('title', 'Authors Management - AklatBayon')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-pen-fancy me-2"></i>Authors Management</h1>
    <button type="button" class="btn btn-success" onclick="openAddModal()">
        <i class="fas fa-plus me-1"></i> Add Author
    </button>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('authors.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by author name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Authors Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Author Name</th>
                        <th>Nationality</th>
                        <th>Books Count</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($authors as $index => $author)
                        <tr>
                            <td>{{ $authors->firstItem() + $index }}</td>
                            <td><strong>{{ $author->name }}</strong></td>
                            <td>{{ $author->nationality ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $author->books_count }}</span></td>
                            <td>{{ $author->created_at->format('M d, Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-action" title="Edit"
                                    onclick="openEditModal({{ $author->id }}, '{{ addslashes($author->name) }}', '{{ addslashes($author->nationality ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('authors.destroy', $author) }}" method="POST" class="d-inline delete-form">
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
                                <i class="fas fa-pen-fancy fa-2x mb-2 d-block"></i>
                                No authors found.
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
    {{ $authors->links() }}
</div>

<!-- Author Modal -->
<div class="modal fade" id="authorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#fff">
                <h5 class="modal-title" id="modalTitle">Add Author</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="authorForm" method="POST" action="{{ route('authors.store') }}">
                @csrf
                <input type="hidden" id="_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Author Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="authorName" class="form-control" placeholder="Enter author name" required>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="authorNationality" class="form-control" placeholder="Enter nationality">
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
    const authorModal = new bootstrap.Modal(document.getElementById('authorModal'));

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Author';
        document.getElementById('authorForm').action = "{{ route('authors.store') }}";
        document.getElementById('_method').value = 'POST';
        document.getElementById('authorName').value = '';
        document.getElementById('authorNationality').value = '';
        authorModal.show();
    }

    function openEditModal(id, name, nationality) {
        document.getElementById('modalTitle').textContent = 'Edit Author';
        document.getElementById('authorForm').action = "{{ url('authors') }}/" + id;
        document.getElementById('_method').value = 'PUT';
        document.getElementById('authorName').value = name;
        document.getElementById('authorNationality').value = nationality;
        authorModal.show();
    }
</script>
@endpush
