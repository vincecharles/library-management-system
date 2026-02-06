<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Book;
use App\Models\Student;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Category;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports index page with report type options.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate a report based on type and filters.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string|in:circulation,overdue,inventory,fines,popular_books,student_activity',
            'date_from'   => 'nullable|date',
            'date_to'     => 'nullable|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $reportType = $validated['report_type'];
        $dateFrom   = $validated['date_from'] ? Carbon::parse($validated['date_from']) : null;
        $dateTo     = $validated['date_to'] ? Carbon::parse($validated['date_to']) : null;

        $data = match ($reportType) {
            'circulation'      => $this->circulationReport($dateFrom, $dateTo),
            'overdue'          => $this->overdueReport(),
            'inventory'        => $this->inventoryReport($validated['category_id'] ?? null),
            'fines'            => $this->finesReport($dateFrom, $dateTo),
            'popular_books'    => $this->popularBooksReport($dateFrom, $dateTo),
            'student_activity' => $this->studentActivityReport($dateFrom, $dateTo),
            default            => [],
        };

        $data['report_type'] = $reportType;
        $data['date_from']   = $dateFrom;
        $data['date_to']     = $dateTo;
        $data['categories']  = Category::all();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Generate Report',
            'module'     => 'Reports',
            'details'    => "Generated '{$reportType}' report." .
                           ($dateFrom ? " From: {$dateFrom->format('M d, Y')}." : '') .
                           ($dateTo ? " To: {$dateTo->format('M d, Y')}." : ''),
            'ip_address' => $request->ip(),
        ]);

        return view('reports.index', $data);
    }

    /**
     * Circulation report: issues and returns within date range.
     */
    private function circulationReport(?Carbon $dateFrom, ?Carbon $dateTo): array
    {
        $query = Transaction::with(['student', 'bookCopy.book', 'issuedBy']);

        if ($dateFrom) {
            $query->whereDate('issue_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('issue_date', '<=', $dateTo);
        }

        $transactions   = $query->latest()->get();
        $totalIssued    = $transactions->count();
        $totalReturned  = $transactions->where('status', 'returned')->count();
        $totalOverdue   = $transactions->where('status', 'issued')->where('due_date', '<', Carbon::today())->count();

        return compact('transactions', 'totalIssued', 'totalReturned', 'totalOverdue');
    }

    /**
     * Overdue books report.
     */
    private function overdueReport(): array
    {
        $overdueTransactions = Transaction::where('status', 'issued')
            ->where('due_date', '<', Carbon::today())
            ->with(['student', 'bookCopy.book'])
            ->orderBy('due_date')
            ->get()
            ->map(function ($transaction) {
                $transaction->overdue_days = Carbon::parse($transaction->due_date)->diffInDays(Carbon::today());
                return $transaction;
            });

        return compact('overdueTransactions');
    }

    /**
     * Inventory report: book counts by category.
     */
    private function inventoryReport(?int $categoryId): array
    {
        $query = Book::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->orderBy('title')->get();

        $summary = [
            'totalTitles'      => $books->count(),
            'totalCopies'      => $books->sum('total_copies'),
            'availableCopies'  => $books->sum('available_copies'),
            'issuedCopies'     => $books->sum('total_copies') - $books->sum('available_copies'),
        ];

        return compact('books', 'summary');
    }

    /**
     * Fines report: fine collection summary.
     */
    private function finesReport(?Carbon $dateFrom, ?Carbon $dateTo): array
    {
        $fineQuery   = Fine::with(['student', 'transaction.bookCopy.book']);
        $paymentQuery = Payment::with('student');

        if ($dateFrom) {
            $fineQuery->whereDate('created_at', '>=', $dateFrom);
            $paymentQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $fineQuery->whereDate('created_at', '<=', $dateTo);
            $paymentQuery->whereDate('created_at', '<=', $dateTo);
        }

        $fines    = $fineQuery->latest()->get();
        $payments = $paymentQuery->latest()->get();

        $summary = [
            'totalFinesAssessed' => $fines->sum('amount'),
            'totalCollected'     => $payments->sum('amount'),
            'totalUnpaid'        => $fines->where('status', 'unpaid')->sum('amount'),
            'totalWaived'        => $fines->where('status', 'waived')->sum('amount'),
        ];

        return compact('fines', 'payments', 'summary');
    }

    /**
     * Popular books report: most borrowed books.
     */
    private function popularBooksReport(?Carbon $dateFrom, ?Carbon $dateTo): array
    {
        $query = Transaction::selectRaw('book_copies.book_id, COUNT(*) as borrow_count')
            ->join('book_copies', 'transactions.book_copy_id', '=', 'book_copies.id');

        if ($dateFrom) {
            $query->whereDate('transactions.issue_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('transactions.issue_date', '<=', $dateTo);
        }

        $popularBooks = $query->groupBy('book_copies.book_id')
            ->orderByDesc('borrow_count')
            ->take(20)
            ->get()
            ->map(function ($item) {
                $item->book = Book::with(['category', 'authors'])->find($item->book_id);
                return $item;
            });

        return compact('popularBooks');
    }

    /**
     * Student activity report: most active borrowers.
     */
    private function studentActivityReport(?Carbon $dateFrom, ?Carbon $dateTo): array
    {
        $query = Transaction::selectRaw('student_id, COUNT(*) as transaction_count')
            ->with('student');

        if ($dateFrom) {
            $query->whereDate('issue_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('issue_date', '<=', $dateTo);
        }

        $studentActivity = $query->groupBy('student_id')
            ->orderByDesc('transaction_count')
            ->take(20)
            ->get()
            ->map(function ($item) {
                $item->student = Student::with(['gradeLevel', 'section'])->find($item->student_id);
                return $item;
            });

        return compact('studentActivity');
    }
}
