<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Transaction;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CirculationController extends Controller
{
    /**
     * Show the issue book form.
     */
    public function issueForm()
    {
        return view('circulation.issue');
    }

    /**
     * Process issuing a book to a student.
     */
    public function issueBook(Request $request)
    {
        $validated = $request->validate([
            'student_no'       => 'required|string',
            'barcode'          => 'required|string',
            'due_date'         => 'nullable|date|after:today',
            'condition_on_borrow' => 'nullable|string|max:50',
            'notes'            => 'nullable|string|max:500',
        ]);

        // Find the student
        $student = Student::where('student_no', $validated['student_no'])
            ->orWhere('library_card_no', $validated['student_no'])
            ->first();

        if (!$student) {
            return back()->withInput()
                ->with('error', 'Student not found. Please check the student number or library card number.');
        }

        // Check if student is active
        if ($student->status !== 'active') {
            return back()->withInput()
                ->with('error', "Student '{$student->full_name}' is not active and cannot borrow books.");
        }

        // Check for overdue books
        $overdueCount = Transaction::where('student_id', $student->id)
            ->where('status', 'issued')
            ->where('due_date', '<', Carbon::today())
            ->count();

        if ($overdueCount > 0) {
            return back()->withInput()
                ->with('error', "Student '{$student->full_name}' has {$overdueCount} overdue book(s). Please return them first.");
        }

        // Check for unpaid fines
        $unpaidFines = Fine::where('student_id', $student->id)
            ->where('status', 'unpaid')
            ->sum('amount');

        if ($unpaidFines > 0) {
            return back()->withInput()
                ->with('error', "Student '{$student->full_name}' has unpaid fines totaling " . number_format($unpaidFines, 2) . ". Please settle fines first.");
        }

        // Check max books allowed
        $currentlyBorrowed = Transaction::where('student_id', $student->id)
            ->where('status', 'issued')
            ->count();

        $maxBooks = $student->max_books_allowed ?? 3;

        if ($currentlyBorrowed >= $maxBooks) {
            return back()->withInput()
                ->with('error', "Student '{$student->full_name}' has already borrowed the maximum of {$maxBooks} book(s).");
        }

        // Find the book copy
        $bookCopy = BookCopy::where('barcode', $validated['barcode'])
            ->orWhere('accession_no', $validated['barcode'])
            ->first();

        if (!$bookCopy) {
            return back()->withInput()
                ->with('error', 'Book copy not found. Please check the barcode or accession number.');
        }

        // Check if book copy is available
        if ($bookCopy->status !== 'available') {
            return back()->withInput()
                ->with('error', "This book copy (Accession: {$bookCopy->accession_no}) is currently '{$bookCopy->status}' and not available for borrowing.");
        }

        // Calculate due date from settings or use provided date
        if (!empty($validated['due_date'])) {
            $dueDate = Carbon::parse($validated['due_date']);
        } else {
            $loanPeriod = Setting::where('key', 'loan_period_days')->value('value') ?? 7;
            $dueDate = Carbon::today()->addDays((int) $loanPeriod);
        }

        // Generate transaction number
        $transactionNo = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Create the transaction
        $transaction = Transaction::create([
            'transaction_no'     => $transactionNo,
            'student_id'         => $student->id,
            'book_copy_id'       => $bookCopy->id,
            'issue_date'         => Carbon::today(),
            'due_date'           => $dueDate,
            'status'             => 'issued',
            'condition_on_borrow' => $validated['condition_on_borrow'] ?? $bookCopy->condition_status,
            'renewals'           => 0,
            'issued_by'          => auth()->id(),
            'notes'              => $validated['notes'] ?? null,
        ]);

        // Update book copy status
        $bookCopy->update(['status' => 'issued']);

        // Update available copies count
        $book = $bookCopy->book;
        if ($book) {
            $book->decrement('available_copies');
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Issue Book',
            'module'     => 'Circulation',
            'details'    => "Issued '{$book->title}' (Accession: {$bookCopy->accession_no}) to {$student->full_name}. Due: {$dueDate->format('M d, Y')}. Transaction: {$transactionNo}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('circulation.issue')
            ->with('success', "Book '{$book->title}' has been issued to {$student->full_name}. Due date: {$dueDate->format('M d, Y')}. Transaction #: {$transactionNo}.");
    }

    /**
     * Show the return book form.
     */
    public function returnForm()
    {
        return view('circulation.return');
    }

    /**
     * Process returning a book.
     */
    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'barcode'             => 'required|string',
            'condition_on_return' => 'nullable|string|max:50',
            'notes'               => 'nullable|string|max:500',
        ]);

        // Find the book copy
        $bookCopy = BookCopy::where('barcode', $validated['barcode'])
            ->orWhere('accession_no', $validated['barcode'])
            ->first();

        if (!$bookCopy) {
            return back()->withInput()
                ->with('error', 'Book copy not found. Please check the barcode or accession number.');
        }

        // Find the active transaction for this book copy
        $transaction = Transaction::where('book_copy_id', $bookCopy->id)
            ->where('status', 'issued')
            ->with(['student', 'bookCopy.book'])
            ->first();

        if (!$transaction) {
            return back()->withInput()
                ->with('error', 'No active borrowing transaction found for this book copy.');
        }

        $student = $transaction->student;
        $book    = $bookCopy->book;

        // Calculate overdue days and fine
        $returnDate  = Carbon::today();
        $dueDate     = Carbon::parse($transaction->due_date);
        $overdueDays = 0;
        $fineAmount  = 0;

        if ($returnDate->greaterThan($dueDate)) {
            $overdueDays = $dueDate->diffInDays($returnDate);
            $finePerDay  = Setting::where('key', 'fine_per_day')->value('value') ?? 5;
            $fineAmount  = $overdueDays * (float) $finePerDay;
        }

        // Update the transaction
        $transaction->update([
            'return_date'         => $returnDate,
            'status'              => 'returned',
            'condition_on_return' => $validated['condition_on_return'] ?? $bookCopy->condition_status,
            'returned_to'         => auth()->id(),
            'notes'               => $validated['notes'] ?? $transaction->notes,
        ]);

        // Update book copy status and condition
        $bookCopy->update([
            'status'           => 'available',
            'condition_status' => $validated['condition_on_return'] ?? $bookCopy->condition_status,
        ]);

        // Update available copies count
        if ($book) {
            $book->increment('available_copies');
        }

        // Create overdue fine if applicable
        if ($fineAmount > 0) {
            Fine::create([
                'transaction_id' => $transaction->id,
                'student_id'     => $student->id,
                'fine_type'      => 'overdue',
                'amount'         => $fineAmount,
                'paid_amount'    => 0,
                'status'         => 'unpaid',
                'notes'          => "Overdue by {$overdueDays} day(s).",
            ]);
        }

        // Check for damage fine
        $conditionOnBorrow = $transaction->condition_on_borrow;
        $conditionOnReturn = $validated['condition_on_return'] ?? $bookCopy->condition_status;

        $conditionRanking = ['excellent' => 1, 'good' => 2, 'fair' => 3, 'poor' => 4, 'damaged' => 5, 'lost' => 6];
        $borrowRank = $conditionRanking[strtolower($conditionOnBorrow)] ?? 2;
        $returnRank = $conditionRanking[strtolower($conditionOnReturn)] ?? 2;

        if ($returnRank > $borrowRank) {
            $damageFine = Setting::where('key', 'damage_fine')->value('value') ?? 50;

            Fine::create([
                'transaction_id' => $transaction->id,
                'student_id'     => $student->id,
                'fine_type'      => 'damage',
                'amount'         => (float) $damageFine,
                'paid_amount'    => 0,
                'status'         => 'unpaid',
                'notes'          => "Book condition changed from '{$conditionOnBorrow}' to '{$conditionOnReturn}'.",
            ]);
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Return Book',
            'module'     => 'Circulation',
            'details'    => "Returned '{$book->title}' (Accession: {$bookCopy->accession_no}) from {$student->full_name}." .
                           ($overdueDays > 0 ? " Overdue by {$overdueDays} day(s). Fine: " . number_format($fineAmount, 2) : ' Returned on time.'),
            'ip_address' => $request->ip(),
        ]);

        $message = "Book '{$book->title}' has been returned by {$student->full_name}.";
        if ($overdueDays > 0) {
            $message .= " Overdue by {$overdueDays} day(s). Fine of " . number_format($fineAmount, 2) . " has been applied.";
        }

        return redirect()->route('circulation.return')
            ->with('success', $message);
    }

    /**
     * Display the circulation history.
     */
    public function history(Request $request)
    {
        $query = Transaction::with(['student', 'bookCopy.book', 'issuedBy', 'returnedTo']);

        // Search by student or book
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_no', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bookCopy', function ($bq) use ($search) {
                      $bq->where('accession_no', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('circulation.history', compact('transactions'));
    }
}
