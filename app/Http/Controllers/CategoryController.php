<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\ActivityLog;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        $category = Category::create($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create Category',
            'module'     => 'Book Management',
            'details'    => "Added category '{$category->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' has been added successfully.");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
        ]);

        $oldName = $category->name;
        $category->update($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Category',
            'module'     => 'Book Management',
            'details'    => "Updated category from '{$oldName}' to '{$category->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' has been updated successfully.");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, Category $category)
    {
        // Check if category has books
        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Cannot delete category '{$category->name}' because it has associated books.");
        }

        $categoryName = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete Category',
            'module'     => 'Book Management',
            'details'    => "Deleted category '{$categoryName}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', "Category '{$categoryName}' has been deleted successfully.");
    }
}
