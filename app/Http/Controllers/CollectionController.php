<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    // Display all collections
    public function index()
    {
        $collections = Auth::user()->collections()->withCount('bookmarks')->get();
        
        return $this->success(
            ['collections' => $collections],
            'Collections retrieved successfully'
        );
    }

    // Create a new collection
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check for duplicate collection name
        $exists = Auth::user()->collections()
            ->where('name', $validated['name'])
            ->exists();
            
        if ($exists) {
            return $this->validationError(
                ['name' => ['You already have a collection with this name']],
                'Duplicate collection name'
            );
        }

        $collection = Auth::user()->collections()->create([
            'name' => $validated['name'],
        ]);

        return $this->created(
            ['collection' => $collection],
            'Collection created successfully'
        );
    }

    // Get a single collection
    public function show($id)
    {
        $collection = Auth::user()->collections()
            ->with('bookmarks.tags')
            ->find($id);
        
        if (!$collection) {
            return $this->notFound('Collection');
        }

        return $this->success(
            ['collection' => $collection],
            'Collection retrieved successfully'
        );
    }

    // Update a collection
    public function update(Request $request, $id)
    {
        $collection = Auth::user()->collections()->find($id);
        
        if (!$collection) {
            return $this->notFound('Collection');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check for duplicate name (excluding current)
        $exists = Auth::user()->collections()
            ->where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->exists();
            
        if ($exists) {
            return $this->validationError(
                ['name' => ['You already have another collection with this name']],
                'Duplicate collection name'
            );
        }

        $collection->update($validated);

        return $this->success(
            ['collection' => $collection],
            'Collection updated successfully'
        );
    }

    // Delete a collection
    public function destroy($id)
    {
        $collection = Auth::user()->collections()->find($id);
        
        if (!$collection) {
            return $this->notFound('Collection');
        }

        $bookmarkCount = $collection->bookmarks()->count();
        $collection->delete();

        return $this->success(
            ['bookmarks_removed' => $bookmarkCount],
            'Collection deleted successfully'
        );
    }

    // Add bookmark to collection
    public function addBookmark(Request $request, $collectionId, $bookmarkId)
    {
        // Find collection and bookmark using scoped queries
        $collection = Auth::user()->collections()->find($collectionId);
        $bookmark = Auth::user()->bookmarks()->find($bookmarkId);
        
        if (!$collection) {
            return $this->notFound('Collection');
        }
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }

        // Check if already in collection
        if ($collection->bookmarks()->where('bookmark_id', $bookmarkId)->exists()) {
            return $this->validationError(
                ['bookmark' => ['Bookmark already in collection']],
                'Bookmark already exists in collection'
            );
        }

        // Add to collection
        $collection->bookmarks()->attach($bookmarkId);

        return $this->success(
            ['collection' => $collection->fresh()->load('bookmarks.tags')],
            'Bookmark added to collection successfully'
        );
    }

    // Remove bookmark from collection
    public function removeBookmark(Request $request, $collectionId, $bookmarkId)
    {
        // Find collection and bookmark using scoped queries
        $collection = Auth::user()->collections()->find($collectionId);
        $bookmark = Auth::user()->bookmarks()->find($bookmarkId);
        
        if (!$collection) {
            return $this->notFound('Collection');
        }
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }

        // Remove from collection
        $collection->bookmarks()->detach($bookmarkId);

        return $this->success(
            ['collection' => $collection->fresh()->load('bookmarks.tags')],
            'Bookmark removed from collection successfully'
        );
    }
}