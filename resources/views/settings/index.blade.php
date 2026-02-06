@extends('layouts.app')

@section('title', 'System Settings - Library Management System')

@section('content')
@php
    $getSetting = function($group, $key, $default = '') use ($settings) {
        return $settings->get($group)?->firstWhere('key', $key)?->value ?? $default;
    };
@endphp

<div class="page-header">
    <h1><i class="fas fa-cog me-2"></i>System Settings</h1>
</div>

<form action="{{ route('settings.update') }}" method="POST">
    @csrf

    {{-- Library Information --}}
    <div class="card">
        <div class="card-header">
            <i class="fas fa-school me-2"></i>Library Information
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="library_name" class="form-label">Library Name</label>
                    <input type="hidden" name="settings[0][key]" value="library_name">
                    <input type="text" class="form-control" id="library_name" name="settings[0][value]"
                           value="{{ old('settings.0.value', $getSetting('general', 'library_name')) }}"
                           placeholder="Enter library name">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="school_name" class="form-label">School Name</label>
                    <input type="hidden" name="settings[1][key]" value="school_name">
                    <input type="text" class="form-control" id="school_name" name="settings[1][value]"
                           value="{{ old('settings.1.value', $getSetting('general', 'school_name')) }}"
                           placeholder="Enter school name">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="academic_year" class="form-label">Academic Year</label>
                    <input type="hidden" name="settings[2][key]" value="academic_year">
                    <input type="text" class="form-control" id="academic_year" name="settings[2][value]"
                           value="{{ old('settings.2.value', $getSetting('general', 'academic_year')) }}"
                           placeholder="e.g. 2025-2026">
                </div>
            </div>
        </div>
    </div>

    {{-- Circulation Settings --}}
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-exchange-alt me-2"></i>Circulation Settings
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="borrowing_days" class="form-label">Student Loan Period (days)</label>
                    <input type="hidden" name="settings[3][key]" value="borrowing_days">
                    <input type="number" class="form-control" id="borrowing_days" name="settings[3][value]"
                           value="{{ old('settings.3.value', $getSetting('circulation', 'borrowing_days', '7')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="teacher_loan_days" class="form-label">Teacher Loan Period (days)</label>
                    <input type="hidden" name="settings[4][key]" value="teacher_loan_days">
                    <input type="number" class="form-control" id="teacher_loan_days" name="settings[4][value]"
                           value="{{ old('settings.4.value', $getSetting('circulation', 'teacher_loan_days', '30')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="employee_loan_days" class="form-label">Employee Loan Period (days)</label>
                    <input type="hidden" name="settings[5][key]" value="employee_loan_days">
                    <input type="number" class="form-control" id="employee_loan_days" name="settings[5][value]"
                           value="{{ old('settings.5.value', $getSetting('circulation', 'employee_loan_days', '21')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_books_student" class="form-label">Max Books - Student</label>
                    <input type="hidden" name="settings[6][key]" value="max_books_student">
                    <input type="number" class="form-control" id="max_books_student" name="settings[6][value]"
                           value="{{ old('settings.6.value', $getSetting('circulation', 'max_books_student', '3')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_books_teacher" class="form-label">Max Books - Teacher</label>
                    <input type="hidden" name="settings[7][key]" value="max_books_teacher">
                    <input type="number" class="form-control" id="max_books_teacher" name="settings[7][value]"
                           value="{{ old('settings.7.value', $getSetting('circulation', 'max_books_teacher', '15')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_books_employee" class="form-label">Max Books - Employee</label>
                    <input type="hidden" name="settings[8][key]" value="max_books_employee">
                    <input type="number" class="form-control" id="max_books_employee" name="settings[8][value]"
                           value="{{ old('settings.8.value', $getSetting('circulation', 'max_books_employee', '10')) }}" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_renewals" class="form-label">Max Renewals</label>
                    <input type="hidden" name="settings[9][key]" value="max_renewals">
                    <input type="number" class="form-control" id="max_renewals" name="settings[9][value]"
                           value="{{ old('settings.9.value', $getSetting('circulation', 'max_renewals', '2')) }}" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="reservation_expiry_days" class="form-label">Reservation Expiry Days</label>
                    <input type="hidden" name="settings[10][key]" value="reservation_expiry_days">
                    <input type="number" class="form-control" id="reservation_expiry_days" name="settings[10][value]"
                           value="{{ old('settings.10.value', $getSetting('circulation', 'reservation_expiry_days', '3')) }}" min="1">
                </div>
            </div>
        </div>
    </div>

    {{-- Fine Settings --}}
    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-money-bill-wave me-2"></i>Fine Settings
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fine_per_day" class="form-label">Fine Rate per Day</label>
                    <input type="hidden" name="settings[11][key]" value="fine_per_day">
                    <div class="input-group">
                        <span class="input-group-text">&#8369;</span>
                        <input type="number" class="form-control" id="fine_per_day" name="settings[11][value]"
                               value="{{ old('settings.11.value', $getSetting('fines', 'fine_per_day', '5.00')) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_fine" class="form-label">Maximum Fine</label>
                    <input type="hidden" name="settings[12][key]" value="max_fine">
                    <div class="input-group">
                        <span class="input-group-text">&#8369;</span>
                        <input type="number" class="form-control" id="max_fine" name="settings[12][value]"
                               value="{{ old('settings.12.value', $getSetting('fines', 'max_fine', '500.00')) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="grace_period" class="form-label">Grace Period (days)</label>
                    <input type="hidden" name="settings[13][key]" value="grace_period">
                    <input type="number" class="form-control" id="grace_period" name="settings[13][value]"
                           value="{{ old('settings.13.value', $getSetting('fines', 'grace_period', '0')) }}" min="0">
                </div>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="mt-4 mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i> Save Settings
        </button>
    </div>
</form>
@endsection
