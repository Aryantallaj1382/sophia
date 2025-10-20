<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MainStory extends Model
{
    protected $fillable = ['cover_image', 'video' ];
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

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
