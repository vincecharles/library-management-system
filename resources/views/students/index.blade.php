@extends('layouts.app')

@section('title', 'Student Management - AklatBayon')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-graduate me-2"></i>Student Management</h1>
    <a href="{{ route('students.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Add Student
    </a>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" action="{{ route('students.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name, student ID, or LRN..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="grade_level" class="form-label">Grade Level</label>
                <select name="grade_level" id="grade_level" class="form-select">
                    <option value="">All Grade Levels</option>
                    @foreach($gradeLevels as $grade)
                        <option value="{{ $grade->id }}" {{ request('grade_level') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="section" class="form-label">Section</label>
                <select name="section" id="section" class="form-select">
                    <option value="">All Sections</option>
                    @if(isset($sections))
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    @endif
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

<!-- Data Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Books Borrowed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_no }}</td>
                        <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                        <td>{{ $student->gradeLevel->name ?? 'N/A' }}</td>
                        <td>{{ $student->section->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $student->transactions()->where('status', 'issued')->count() }}</span>
                        </td>
                        <td>
                            @if($student->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($student->status == 'inactive')
                                <span class="badge bg-secondary">Inactive</span>
                            @elseif($student->status == 'graduated')
                                <span class="badge bg-info">Graduated</span>
                            @elseif($student->status == 'transferred')
                                <span class="badge bg-warning text-dark">Transferred</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                            No students found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-3">
    {{ $students->links() }}
</div>
@endsection
