<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;

class InventoryController extends Controller
{
    /**
     * Display the book inventory overview.
     */
    public function index(Request $request)
    {
        $query = BookCopy::with(['book.category', 'book.authors']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('accession_no', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition_status', $request->condition);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('book', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        $copies     = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::all();

        // Summary statistics
        $summary = [
            'totalCopies'     => BookCopy::count(),
            'availableCopies' => BookCopy::where('status', 'available')->count(),
            'issuedCopies'    => BookCopy::where('status', 'issued')->count(),
            'damagedCopies'   => BookCopy::where('condition_status', 'damaged')->count(),
            'lostCopies'      => BookCopy::where('status', 'lost')->count(),
            'totalTitles'     => Book::count(),
        ];

        return view('inventory.index', compact('copies', 'categories', 'summary'));
    }
}
