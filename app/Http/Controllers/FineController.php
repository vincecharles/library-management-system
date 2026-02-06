<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Student;
use App\Models\ActivityLog;

class FineController extends Controller
{
    /**
     * Display a listing of fines.
     */
    public function index(Request $request)
    {
        $query = Fine::with(['student', 'transaction.bookCopy.book']);

        // Search by student
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_no', 'like', "%{$search}%")
                  ->orWhere('library_card_no', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by fine type
        if ($request->filled('fine_type')) {
            $query->where('fine_type', $request->fine_type);
        }

        $fines = $query->latest()->paginate(15)->withQueryString();

        // Summary statistics
        $totalUnpaid   = Fine::where('status', 'unpaid')->sum('amount');
        $totalCollected = Payment::sum('amount');

        return view('fines.index', compact('fines', 'totalUnpaid', 'totalCollected'));
    }

    /**
     * Collect (pay) a fine.
     */
    public function collect(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . ($fine->amount - $fine->paid_amount),
            'payment_method' => 'required|string|in:cash,gcash,other',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Create payment record
        $payment = Payment::create([
            'fine_id'        => $fine->id,
            'student_id'     => $fine->student_id,
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'received_by'    => auth()->id(),
            'notes'          => $validated['notes'] ?? null,
        ]);

        // Update fine paid amount and status
        $newPaidAmount = $fine->paid_amount + $validated['amount'];
        $fine->update([
            'paid_amount' => $newPaidAmount,
            'status'      => $newPaidAmount >= $fine->amount ? 'paid' : 'partial',
        ]);

        $student = $fine->student;

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Collect Fine',
            'module'     => 'Fines',
            'details'    => "Collected fine payment of " . number_format($validated['amount'], 2) . " from {$student->full_name}. " .
                           "Fine type: {$fine->fine_type}. Payment method: {$validated['payment_method']}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('fines.index')
            ->with('success', "Payment of " . number_format($validated['amount'], 2) . " has been collected from {$student->full_name}.");
    }

    /**
     * Waive a fine.
     */
    public function waive(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $student = $fine->student;

        $fine->update([
            'status' => 'waived',
            'notes'  => $validated['notes'] ?? "Fine waived by " . auth()->user()->name . ".",
        ]);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Waive Fine',
            'module'     => 'Fines',
            'details'    => "Waived fine of " . number_format($fine->amount, 2) . " for {$student->full_name}. " .
                           "Fine type: {$fine->fine_type}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('fines.index')
            ->with('success', "Fine of " . number_format($fine->amount, 2) . " for {$student->full_name} has been waived.");
    }
}
