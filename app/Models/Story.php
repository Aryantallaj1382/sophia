<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Story extends Model
{
    protected $fillable = ['cover_image', 'video' , 'professor_id'];
    public function getCoverImageAttribute($value)
    {

        return $value ? url('public/'.$value) : null;
    }
    public function getImageAttribute()
    {
        return $this->cover_image? url('public/'.$this->cover_image) : null;
    }
    public function getVideoAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
