<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['name', 'color', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function bookmarks()
    {
        return $this->belongsToMany(Bookmark::class, 'collection_bookmark');
    }
}
