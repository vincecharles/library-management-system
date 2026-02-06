<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'assistant';

        switch ($role) {
            case 'Administrator':
                return $this->adminDashboard();

            case 'Librarian':
                return $this->librarianDashboard();

            default:
                return $this->librarianDashboard();
        }
    }

    /**
     * Admin dashboard with system-wide statistics.
     */
    private function adminDashboard()
    {
        $data = [
            'totalBooks'      => Book::count(),
            'activeStudents'  => Student::where('status', 'active')->count(),
            'booksIssued'     => Transaction::where('status', 'issued')->count(),
            'overdueBooks'    => Transaction::where('status', 'issued')
                                    ->where('due_date', '<', Carbon::today())
                                    ->count(),
            'totalStudents'   => Student::count(),
            'totalFinesCollected' => Payment::sum('amount'),
            'pendingFines'    => Fine::where('status', 'unpaid')->sum('amount'),
            'recentTransactions' => Transaction::with(['student', 'bookCopy.book'])
                                    ->latest()
                                    ->take(10)
                                    ->get(),
            'recentActivities' => ActivityLog::with('user')
                                    ->latest()
                                    ->take(10)
                                    ->get(),
        ];

        return view('dashboard.admin', $data);
    }

    /**
     * Librarian dashboard with daily operations statistics.
     */
    private function librarianDashboard()
    {
        $today = Carbon::today();

        $data = [
            'todayIssues'     => Transaction::whereDate('issue_date', $today)->count(),
            'todayReturns'    => Transaction::whereDate('return_date', $today)->count(),
            'dueToday'        => Transaction::where('status', 'issued')
                                    ->whereDate('due_date', $today)
                                    ->count(),
            'finesCollected'  => Payment::whereDate('created_at', $today)->sum('amount'),
            'booksDueToday'   => Transaction::where('status', 'issued')
                                    ->whereDate('due_date', $today)
                                    ->with(['student', 'bookCopy.book'])
                                    ->get(),
            'overdueBooks'    => Transaction::where('status', 'issued')
                                    ->where('due_date', '<', $today)
                                    ->with(['student', 'bookCopy.book'])
                                    ->take(10)
                                    ->get(),
            'recentTransactions' => Transaction::with(['student', 'bookCopy.book'])
                                    ->latest()
                                    ->take(10)
                                    ->get(),
        ];

        return view('dashboard.librarian', $data);
    }
}
