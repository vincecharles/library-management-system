@extends('layouts.app')

@section('title', 'Add New User - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-plus me-2"></i>Add New User</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-user-edit me-1"></i> User Information
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter full name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Enter username" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email address" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                    <select name="user_type" id="user_type" class="form-select @error('user_type') is-invalid @enderror" required>
                        <option value="">Select User Type</option>
                        <option value="librarian" {{ old('user_type') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                        <option value="student_assistant" {{ old('user_type') == 'student_assistant' ? 'selected' : '' }}>Student Assistant</option>
                        <option value="student" {{ old('user_type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('user_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    </select>
                    @error('user_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6" id="faculty-subtype-wrapper" style="{{ old('user_type') === 'faculty' ? '' : 'display:none' }}">
                    <label for="faculty_subtype" class="form-label">Faculty Sub-Type <span class="text-danger">*</span></label>
                    <select name="faculty_subtype" id="faculty_subtype" class="form-select @error('faculty_subtype') is-invalid @enderror">
                        <option value="">Select Faculty Sub-Type</option>
                        <option value="teacher" {{ old('faculty_subtype') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="non_teacher" {{ old('faculty_subtype') == 'non_teacher' ? 'selected' : '' }}>Non-Teacher</option>
                        <option value="staff" {{ old('faculty_subtype') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('faculty_subtype')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm password" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Actor Types Reference</label>
                <div>
                    <span class="badge bg-dark me-1"><i class="fas fa-shield-alt me-1"></i> Administrator</span>
                    <span class="badge bg-danger me-1"><i class="fas fa-user-tie me-1"></i> Librarian</span>
                    <span class="badge bg-success me-1"><i class="fas fa-user me-1"></i> Student Assistant</span>
                    <span class="badge bg-info me-1"><i class="fas fa-user-graduate me-1"></i> Student</span>
                    <span class="badge bg-warning text-dark me-1"><i class="fas fa-chalkboard-teacher me-1"></i> Faculty</span>
                </div>
                <small class="text-muted mt-1 d-block">
                    Faculty members have sub-types: Teacher, Non-Teacher, or Staff.
                    Each role has different access permissions within the system.
                </small>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Save User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('user_type').addEventListener('change', function() {
    const wrapper = document.getElementById('faculty-subtype-wrapper');
    const select = document.getElementById('faculty_subtype');
    if (this.value === 'faculty') {
        wrapper.style.display = '';
        select.setAttribute('required', 'required');
    } else {
        wrapper.style.display = 'none';
        select.removeAttribute('required');
        select.value = '';
    }
});
</script>
@endpush
