<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Bookmark;
class Tag extends Model  // Make sure it's Tag (singular), not Tags
{
    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Bookmark::class, 'bookmark_tag');
    }
}