<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Publisher;
use App\Models\ActivityLog;

class PublisherController extends Controller
{
    /**
     * Display a listing of publishers.
     */
    public function index(Request $request)
    {
        $query = Publisher::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $publishers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('publishers.index', compact('publishers'));
    }

    /**
     * Store a newly created publisher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:publishers,name',
        ]);

        $publisher = Publisher::create($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Create Publisher',
            'module'     => 'Book Management',
            'details'    => "Added publisher '{$publisher->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('publishers.index')
            ->with('success', "Publisher '{$publisher->name}' has been added successfully.");
    }

    /**
     * Update the specified publisher.
     */
    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('publishers', 'name')->ignore($publisher->id)],
        ]);

        $oldName = $publisher->name;
        $publisher->update($validated);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Update Publisher',
            'module'     => 'Book Management',
            'details'    => "Updated publisher from '{$oldName}' to '{$publisher->name}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('publishers.index')
            ->with('success', "Publisher '{$publisher->name}' has been updated successfully.");
    }

    /**
     * Remove the specified publisher.
     */
    public function destroy(Request $request, Publisher $publisher)
    {
        // Check if publisher has books
        if ($publisher->books()->count() > 0) {
            return redirect()->route('publishers.index')
                ->with('error', "Cannot delete publisher '{$publisher->name}' because they have associated books.");
        }

        $publisherName = $publisher->name;
        $publisher->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'Delete Publisher',
            'module'     => 'Book Management',
            'details'    => "Deleted publisher '{$publisherName}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('publishers.index')
            ->with('success', "Publisher '{$publisherName}' has been deleted successfully.");
    }
}
