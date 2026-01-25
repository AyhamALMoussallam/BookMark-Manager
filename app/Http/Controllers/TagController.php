<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    // Display all tags for the logged-in user
    public function index()
    {
        $tags = Auth::user()->tags()->withCount('bookmarks')->get();
        
        return $this->success(
            ['tags' => $tags],
            'Tags retrieved successfully'
        );
    }

    // Update a tag
    public function update(Request $request, $id)
    {
        $tag = Auth::user()->tags()->find($id);
        
        if (!$tag) {
            return $this->notFound('Tag');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $id . ',id,user_id,' . Auth::id(),
        ]);

        // Get bookmark count before update
        $bookmarkCount = $tag->bookmarks()->count();
        
        // Update the tag name
        $oldName = $tag->name;
        $tag->update(['name' => $validated['name']]);

        return $this->success([
            'tag' => $tag,
            'updated_in_bookmarks' => $bookmarkCount,
            'bookmarks_affected' => $bookmarkCount
        ], 'Tag updated successfully');
    }

    // Delete a tag
    public function destroy($id)
    {
        $tag = Auth::user()->tags()->find($id);
        
        if (!$tag) {
            return $this->notFound('Tag');
        }

        // Get bookmark count before deletion
        $bookmarkCount = $tag->bookmarks()->count();
        
        // Delete the tag (cascade deletes bookmark_tag entries)
        $tag->delete();

        return $this->success([
            'removed_from_bookmarks' => $bookmarkCount,
            'bookmarks_affected' => $bookmarkCount
        ], 'Tag deleted successfully');
    }
}