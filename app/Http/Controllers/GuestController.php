<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;

class GuestController extends Controller
{
    /**
     * Display the public book catalog.
     */
    public function catalog(Request $request)
    {
        $query = Book::with(['category', 'authors', 'publishers'])
            ->where('status', 'active');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('authors', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('publishers', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by author
        if ($request->filled('author')) {
            $query->whereHas('authors', function ($q) use ($request) {
                $q->where('authors.id', $request->author);
            });
        }

        // Filter by availability
        if ($request->filled('available') && $request->available == '1') {
            $query->where('available_copies', '>', 0);
        }

        $books      = $query->orderBy('title')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $authors    = Author::orderBy('name')->get();

        return view('guest.catalog', compact('books', 'categories', 'authors'));
    }
}
