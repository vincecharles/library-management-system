@extends('layouts.app')

@section('title', 'Reports & Analytics - Library Management System')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-alt me-2"></i>Generate Report
    </div>
    <div class="card-body p-4">
        <form id="reportForm" method="GET" action="{{ route('reports.generate') }}">
            {{-- Report Type Section --}}
            <h6 class="fw-bold text-uppercase text-muted mb-3">
                <i class="fas fa-list me-1"></i> Report Type
            </h6>

            <div class="row mb-4">
                {{-- Circulation Reports --}}
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <i class="fas fa-exchange-alt me-1 text-primary"></i> Circulation Reports
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="report_type" id="daily_transaction" value="daily_transaction" checked>
                                <label class="form-check-label" for="daily_transaction">Daily Transaction Report</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="report_type" id="monthly_summary" value="monthly_summary">
                                <label class="form-check-label" for="monthly_summary">Monthly Summary</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Book Reports --}}
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <i class="fas fa-book me-1 text-success"></i> Book Reports
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="report_type" id="most_borrowed" value="most_borrowed">
                                <label class="form-check-label" for="most_borrowed">Most Borrowed Books</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="report_type" id="book_inventory" value="book_inventory">
                                <label class="form-check-label" for="book_inventory">Book Inventory Report</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Student Reports --}}
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <i class="fas fa-user-graduate me-1 text-info"></i> Student Reports
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="report_type" id="active_borrowers" value="active_borrowers">
                                <label class="form-check-label" for="active_borrowers">Active Borrowers</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="report_type" id="overdue_students" value="overdue_students">
                                <label class="form-check-label" for="overdue_students">Students with Overdue</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fine Reports --}}
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <i class="fas fa-money-bill-wave me-1 text-warning"></i> Fine Reports
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="report_type" id="fine_collection" value="fine_collection">
                                <label class="form-check-label" for="fine_collection">Fine Collection Summary</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="report_type" id="outstanding_fines" value="outstanding_fines">
                                <label class="form-check-label" for="outstanding_fines">Outstanding Fines Report</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Date Range --}}
            <h6 class="fw-bold text-uppercase text-muted mb-3">
                <i class="fas fa-calendar-alt me-1"></i> Date Range
            </h6>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
            </div>

            {{-- Export Format --}}
            <h6 class="fw-bold text-uppercase text-muted mb-3">
                <i class="fas fa-download me-1"></i> Export Format
            </h6>
            <div class="row mb-4">
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="export_format" id="format_excel" value="excel" checked>
                        <label class="btn btn-outline-success" for="format_excel">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </label>

                        <input type="radio" class="btn-check" name="export_format" id="format_pdf" value="pdf">
                        <label class="btn btn-outline-danger" for="format_pdf">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </label>

                        <input type="radio" class="btn-check" name="export_format" id="format_print" value="print">
                        <label class="btn btn-outline-info" for="format_print">
                            <i class="fas fa-print me-1"></i> Print
                        </label>
                    </div>
                </div>
            </div>

            {{-- Generate Button --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-cogs me-2"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
