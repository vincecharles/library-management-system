<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Author;
use App\Models\ActivityLog;

class AuthorController extends Controller
{
    /**
     * Display a listing of authors.
     */
    public function index(Request $request)
    {
        $query = Author::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $authors = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('authors.index', compact('authors'));
    }

    /**
     * Store a newly created author.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:authors,name',
        ]);

        $author = Author::create($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create Author',
            'module'     => 'Book Management',
            'details'    => "Added author '{$author->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('authors.index')
            ->with('success', "Author '{$author->name}' has been added successfully.");
    }

    /**
     * Update the specified author.
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('authors', 'name')->ignore($author->id)],
        ]);

        $oldName = $author->name;
        $author->update($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Author',
            'module'     => 'Book Management',
            'details'    => "Updated author from '{$oldName}' to '{$author->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('authors.index')
            ->with('success', "Author '{$author->name}' has been updated successfully.");
    }

    /**
     * Remove the specified author.
     */
    public function destroy(Request $request, Author $author)
    {
        // Check if author has books
        if ($author->books()->count() > 0) {
            return redirect()->route('authors.index')
                ->with('error', "Cannot delete author '{$author->name}' because they have associated books.");
        }

        $authorName = $author->name;
        $author->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete Author',
            'module'     => 'Book Management',
            'details'    => "Deleted author '{$authorName}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('authors.index')
            ->with('success', "Author '{$authorName}' has been deleted successfully.");
    }
}
