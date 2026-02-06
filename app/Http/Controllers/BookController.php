<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\ActivityLog;

class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request)
    {
        $query = Book::with(['category', 'authors', 'publishers']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('authors', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $books      = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = Category::all();
        $authors    = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('books.create', compact('categories', 'authors', 'publishers'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'             => 'nullable|string|max:20|unique:books,isbn',
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description'      => 'nullable|string',
            'cover_image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shelf_location'   => 'nullable|string|max:50',
            'total_copies'     => 'required|integer|min:1',
            'status'           => 'required|in:active,inactive',
            'authors'          => 'nullable|array',
            'authors.*'        => 'exists:authors,id',
            'publishers'       => 'nullable|array',
            'publishers.*'     => 'exists:publishers,id',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $validated['available_copies'] = $validated['total_copies'];

        $book = Book::create($validated);

        // Attach authors and publishers
        if ($request->filled('authors')) {
            $book->authors()->attach($request->authors);
        }

        if ($request->filled('publishers')) {
            $book->publishers()->attach($request->publishers);
        }

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create Book',
            'module'     => 'Book Management',
            'details'    => "Added book '{$book->title}' (ISBN: {$book->isbn}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('books.index')
            ->with('success', "Book '{$book->title}' has been added successfully.");
    }

    /**
     * Display the book details.
     */
    public function show(Book $book)
    {
        $book->load(['category', 'authors', 'publishers']);

        $copies = BookCopy::where('book_id', $book->id)->get();

        return view('books.show', compact('book', 'copies'));
    }

    /**
     * Show the form for editing a book.
     */
    public function edit(Book $book)
    {
        $book->load(['authors', 'publishers']);

        $categories = Category::all();
        $authors    = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories', 'authors', 'publishers'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'isbn'             => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book->id)],
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'description'      => 'nullable|string',
            'cover_image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'shelf_location'   => 'nullable|string|max:50',
            'total_copies'     => 'required|integer|min:1',
            'status'           => 'required|in:active,inactive',
            'authors'          => 'nullable|array',
            'authors.*'        => 'exists:authors,id',
            'publishers'       => 'nullable|array',
            'publishers.*'     => 'exists:publishers,id',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        // Recalculate available copies based on the difference
        $copyDifference = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = max(0, $book->available_copies + $copyDifference);

        $book->update($validated);

        // Sync authors and publishers
        $book->authors()->sync($request->authors ?? []);
        $book->publishers()->sync($request->publishers ?? []);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Book',
            'module'     => 'Book Management',
            'details'    => "Updated book '{$book->title}' (ISBN: {$book->isbn}).",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('books.index')
            ->with('success', "Book '{$book->title}' has been updated successfully.");
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Request $request, Book $book)
    {
        // Check if any copies are currently issued
        $issuedCopies = BookCopy::where('book_id', $book->id)
            ->where('status', 'issued')
            ->count();

        if ($issuedCopies > 0) {
            return redirect()->route('books.index')
                ->with('error', "Cannot delete book '{$book->title}' because some copies are currently issued.");
        }

        $bookTitle = $book->title;

        // Delete cover image if exists
        if ($book->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover_image);
        }

        // Detach relationships
        $book->authors()->detach();
        $book->publishers()->detach();

        $book->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete Book',
            'module'     => 'Book Management',
            'details'    => "Deleted book '{$bookTitle}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('books.index')
            ->with('success', "Book '{$bookTitle}' has been deleted successfully.");
    }

    /**
     * AJAX search for books (used in circulation forms).
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $copies = BookCopy::where('status', 'available')
            ->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('accession_no', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                  });
            })
            ->with('book.category')
            ->take(10)
            ->get()
            ->map(function ($copy) {
                return [
                    'id'             => $copy->id,
                    'barcode'        => $copy->barcode,
                    'accession_no'   => $copy->accession_no,
                    'title'          => $copy->book->title ?? '',
                    'isbn'           => $copy->book->isbn ?? '',
                    'category'       => $copy->book->category->name ?? '',
                    'status'         => $copy->status,
                    'condition'      => $copy->condition_status,
                ];
            });

        return response()->json($copies);
    }
}
