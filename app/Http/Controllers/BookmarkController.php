<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    // Get all bookmarks with filters
    public function index(Request $request)
{
    $query = Auth::user()->bookmarks()->with('tags');
    
    // Filter by favorite if requested
    if ($request->has('favorite')) {
        $query->where('is_favorite', filter_var($request->favorite, FILTER_VALIDATE_BOOLEAN));
    }
    
    // Filter by tags if provided
    if ($request->has('tags')) {
        $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
        $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags);
        });
    }
    
    // Search in title/description if provided
    if ($request->has('search')) {
        $searchTerm = '%' . $request->search . '%';
        $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', $searchTerm)
              ->orWhere('description', 'like', $searchTerm);
        });
    }
    
    // NEW: Filter by collection if provided
    if ($request->has('collection')) {
        $collectionId = $request->collection;
        $query->whereHas('collections', function ($q) use ($collectionId) {
            $q->where('collections.id', $collectionId);
        });
    }
    
    $bookmarks = $query->latest()->get();
    
    // Prepare response with filters
    $responseData = [
        'bookmarks' => $bookmarks,
        'filters_applied' => [
            'favorite' => $request->has('favorite') ? filter_var($request->favorite, FILTER_VALIDATE_BOOLEAN) : null,
            'tags' => $request->has('tags') ? $tags : null,
            'search' => $request->has('search') ? $request->search : null,
            'collection' => $request->has('collection') ? $request->collection : null,
        ]
    ];
    
    return $this->success($responseData, 'Bookmarks retrieved successfully');
}

    // Create a new bookmark
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_favorite' => 'nullable|boolean',
        ]);
    
        $domain = parse_url($validated['url'], PHP_URL_HOST);
        $faviconUrl = "https://www.google.com/s2/favicons?domain={$domain}&sz=64";
        $title = $validated['title'] ?? $domain ?? 'Untitled';
    
        $bookmark = Auth::user()->bookmarks()->create([
            'url' => $validated['url'],
            'title' => $title,
            'description' => $validated['description'] ?? null,
            'favicon_url' => $faviconUrl,
            'is_favorite' => $validated['is_favorite'] ?? false,
        ]);
    
        // Handle tags
        if (!empty($validated['tags'])) {
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName, 'user_id' => Auth::id()]
                );
                $bookmark->tags()->attach($tag->id);
            }
        }
    
        return $this->created(
            ['bookmark' => $bookmark->load('tags')],
            'Bookmark created successfully'
        );
    }

    // Get a single bookmark
    public function show($id)
    {
        $bookmark = Auth::user()->bookmarks()
            ->with('tags')
            ->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }
        
        return $this->success(
            ['bookmark' => $bookmark],
            'Bookmark retrieved successfully'
        );
    }

    // Update a bookmark
    public function update(Request $request, $id)
    {
        $bookmark = Auth::user()->bookmarks()->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }
    
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);
    
        $oldTags = $bookmark->tags()->get();
        $bookmark->update($validated);
    
        // Sync tags if provided
        if ($request->has('tags')) {
            $bookmark->tags()->detach();
            
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName, 'user_id' => Auth::id()]
                );
                $bookmark->tags()->attach($tag->id);
            }
            
            // Clean up old tags
            foreach ($oldTags as $tag) {
                if ($tag->bookmarks()->count() === 0) {
                    $tag->delete();
                }
            }
        }
    
        return $this->success(
            ['bookmark' => $bookmark->load('tags')],
            'Bookmark updated successfully'
        );
    }

    // Delete a bookmark
    public function destroy($id)
    {
        $bookmark = Auth::user()->bookmarks()->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }
    
        $tags = $bookmark->tags()->get();
        $bookmark->delete();
        
        // Clean up orphaned tags
        foreach ($tags as $tag) {
            if ($tag->bookmarks()->count() === 0) {
                $tag->delete();
            }
        }
    
        return $this->success(
            [],
            'Bookmark deleted successfully'
        );
    }

    // Mark as favorite
    public function favorite($id)
    {
        $bookmark = Auth::user()->bookmarks()->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }

        $bookmark->is_favorite = true;
        $bookmark->save();
        
        return $this->success(
            ['bookmark' => $bookmark->load('tags')],
            'Bookmark marked as favorite'
        );
    }

    // Unmark favorite
    public function unfavorite($id)
    {
        $bookmark = Auth::user()->bookmarks()->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }

        $bookmark->is_favorite = false;
        $bookmark->save();
        
        return $this->success(
            ['bookmark' => $bookmark->load('tags')],
            'Bookmark removed from favorites'
        );
    }

    // Toggle favorite
    public function toggleFavorite($id)
    {
        $bookmark = Auth::user()->bookmarks()->find($id);
        
        if (!$bookmark) {
            return $this->notFound('Bookmark');
        }

        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();
        
        $message = $bookmark->is_favorite 
            ? 'Bookmark marked as favorite' 
            : 'Bookmark removed from favorites';
        
        return $this->success(
            ['bookmark' => $bookmark->load('tags')],
            $message
        );
    }

    // Get all favorites
    public function favorites()
    {
        $favorites = Auth::user()->bookmarks()
            ->where('is_favorite', true)
            ->with('tags')
            ->latest()
            ->get();
        
        return $this->success(
            ['favorites' => $favorites],
            'Favorites retrieved successfully'
        );
    }
}