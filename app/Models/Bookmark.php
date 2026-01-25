<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Collection;
class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'title',
        'description', 
        'favicon_url',
        'is_favorite',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'bookmark_tag');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_bookmark');
    }
}