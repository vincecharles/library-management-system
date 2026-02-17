@extends('layouts.app')

@section('title', 'Edit User - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-edit me-2"></i>Edit User</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-user-edit me-1"></i> User Information — {{ $user->name }}
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" placeholder="Enter username" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
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
                        <option value="librarian" {{ old('user_type', $user->user_type) == 'librarian' ? 'selected' : '' }}>Librarian</option>
                        <option value="student_assistant" {{ old('user_type', $user->user_type) == 'student_assistant' ? 'selected' : '' }}>Student Assistant</option>
                        <option value="student" {{ old('user_type', $user->user_type) == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="faculty" {{ old('user_type', $user->user_type) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                    </select>
                    @error('user_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6" id="faculty-subtype-wrapper" style="{{ old('user_type', $user->user_type) === 'faculty' ? '' : 'display:none' }}">
                    <label for="faculty_subtype" class="form-label">Faculty Sub-Type <span class="text-danger">*</span></label>
                    <select name="faculty_subtype" id="faculty_subtype" class="form-select @error('faculty_subtype') is-invalid @enderror">
                        <option value="">Select Faculty Sub-Type</option>
                        <option value="teacher" {{ old('faculty_subtype', $user->faculty_subtype) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="non_teacher" {{ old('faculty_subtype', $user->faculty_subtype) == 'non_teacher' ? 'selected' : '' }}>Non-Teacher</option>
                        <option value="staff" {{ old('faculty_subtype', $user->faculty_subtype) == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('faculty_subtype')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm new password">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="locked" {{ old('status', $user->status) == 'locked' ? 'selected' : '' }}>Locked</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if($user->user_type)
            <div class="mb-3">
                <label class="form-label">Current Classification</label>
                <div>
                    <span class="badge bg-info">{{ $user->user_type_label }}</span>
                    @if($user->role)
                        <span class="badge bg-secondary ms-1">Role: {{ $user->role->name }}</span>
                    @endif
                </div>
            </div>
            @endif

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Update User
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
