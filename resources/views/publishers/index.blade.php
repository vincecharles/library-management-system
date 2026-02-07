@extends('layouts.app')

@section('title', 'Publishers Management - AklatBayon')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-building me-2"></i>Publishers Management</h1>
    <button type="button" class="btn btn-success" onclick="openAddModal()">
        <i class="fas fa-plus me-1"></i> Add Publisher
    </button>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('publishers.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by publisher name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Publishers Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Publisher Name</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Books Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publishers as $index => $publisher)
                        <tr>
                            <td>{{ $publishers->firstItem() + $index }}</td>
                            <td><strong>{{ $publisher->name }}</strong></td>
                            <td>{{ $publisher->location ?? 'N/A' }}</td>
                            <td>{{ $publisher->contact ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $publisher->books_count }}</span></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-action" title="Edit"
                                    onclick="openEditModal({{ $publisher->id }}, '{{ addslashes($publisher->name) }}', '{{ addslashes($publisher->location ?? '') }}', '{{ addslashes($publisher->contact ?? '') }}', '{{ addslashes($publisher->email ?? '') }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('publishers.destroy', $publisher) }}" method="POST" class="d-inline delete-form">
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
                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                No publishers found.
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
    {{ $publishers->links() }}
</div>

<!-- Publisher Modal -->
<div class="modal fade" id="publisherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#fff">
                <h5 class="modal-title" id="modalTitle">Add Publisher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="publisherForm" method="POST" action="{{ route('publishers.store') }}">
                @csrf
                <input type="hidden" id="_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="publisherName" class="form-label">Publisher Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="publisherName" class="form-control" placeholder="Enter publisher name" required>
                    </div>
                    <div class="mb-3">
                        <label for="publisherLocation" class="form-label">Location</label>
                        <input type="text" name="location" id="publisherLocation" class="form-control" placeholder="Enter location">
                    </div>
                    <div class="mb-3">
                        <label for="publisherContact" class="form-label">Contact</label>
                        <input type="text" name="contact" id="publisherContact" class="form-control" placeholder="Enter contact number">
                    </div>
                    <div class="mb-3">
                        <label for="publisherEmail" class="form-label">Email</label>
                        <input type="email" name="email" id="publisherEmail" class="form-control" placeholder="Enter email address">
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
    const publisherModal = new bootstrap.Modal(document.getElementById('publisherModal'));

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Publisher';
        document.getElementById('publisherForm').action = "{{ route('publishers.store') }}";
        document.getElementById('_method').value = 'POST';
        document.getElementById('publisherName').value = '';
        document.getElementById('publisherLocation').value = '';
        document.getElementById('publisherContact').value = '';
        document.getElementById('publisherEmail').value = '';
        publisherModal.show();
    }

    function openEditModal(id, name, location, contact, email) {
        document.getElementById('modalTitle').textContent = 'Edit Publisher';
        document.getElementById('publisherForm').action = "{{ url('publishers') }}/" + id;
        document.getElementById('_method').value = 'PUT';
        document.getElementById('publisherName').value = name;
        document.getElementById('publisherLocation').value = location;
        document.getElementById('publisherContact').value = contact;
        document.getElementById('publisherEmail').value = email;
        publisherModal.show();
    }
</script>
@endpush
