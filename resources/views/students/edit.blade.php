@extends('layouts.app')

@section('title', 'Edit Student - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-edit me-2"></i>Edit Student</h1>
    <a href="{{ route('students.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Students
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-user-graduate me-1"></i> Student Information
    </div>
    <div class="card-body">
        <form action="{{ route('students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_no" class="form-label">Student No.</label>
                        <input type="text" id="student_no" class="form-control" value="{{ $student->student_no }}" readonly disabled>
                        <small class="text-muted">This field cannot be changed.</small>
                    </div>

                    <div class="mb-3">
                        <label for="lrn" class="form-label">LRN (Learner Reference Number)</label>
                        <input type="text" name="lrn" id="lrn" class="form-control @error('lrn') is-invalid @enderror" value="{{ old('lrn', $student->lrn) }}" placeholder="Enter LRN (optional)">
                        @error('lrn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name) }}" placeholder="Enter first name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $student->middle_name) }}" placeholder="Enter middle name">
                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->last_name) }}" placeholder="Enter last name" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="grade_level_id" class="form-label">Grade Level <span class="text-danger">*</span></label>
                        <select name="grade_level_id" id="grade_level_id" class="form-select @error('grade_level_id') is-invalid @enderror" required>
                            <option value="">Select Grade Level</option>
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_level_id', $student->grade_level_id) == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        @error('grade_level_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ old('section_id', $student->section_id) == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="school_year" class="form-label">School Year</label>
                        <input type="text" name="school_year" id="school_year" class="form-control @error('school_year') is-invalid @enderror" value="{{ old('school_year', $student->school_year) }}" placeholder="e.g., 2025-2026">
                        @error('school_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $student->contact_number) }}" placeholder="Enter contact number">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email) }}" placeholder="Enter email address">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Full Width Fields -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter complete address">{{ old('address', $student->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="guardian_name" class="form-label">Guardian Name</label>
                    <input type="text" name="guardian_name" id="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror" value="{{ old('guardian_name', $student->guardian_name) }}" placeholder="Enter guardian's full name">
                    @error('guardian_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="guardian_contact" class="form-label">Guardian Contact</label>
                    <input type="text" name="guardian_contact" id="guardian_contact" class="form-control @error('guardian_contact') is-invalid @enderror" value="{{ old('guardian_contact', $student->guardian_contact) }}" placeholder="Enter guardian's contact number">
                    @error('guardian_contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Update Student
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
