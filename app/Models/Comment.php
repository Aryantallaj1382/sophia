<?php

namespace App\Models;

use App\Models\Like;
use App\Models\Dislike;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'body',
        'video_url',
        'voice_url',
        'commentable_id',
        'commentable_type',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getVoiceUrlAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }

    public function getTypeAttribute($value)
    {
        if ($this->commentable_type == Professor::class)
        {
            $type = 'professor';
        }
        if ($this->commentable_type == Story::class)
        {
            $type = 'story';
        }
        if ($this->commentable_type == GroupClass::class)
        {
            $type = 'group';
        }
        if ($this->commentable_type == Webinar::class)
        {
            $type = 'webinar';
        }
        if ($this->commentable_type == Book::class)
        {
            $type = 'Book';
        }
        return $type ?? '';
    }
    public function commentable()
    {
        return $this->morphTo();
    }

    public function getVideoUrlAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }




    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikable');
    }

    public static function getCommentsForAdmin(?string $type = null, ?string $commentableType = null): \Illuminate\Database\Eloquent\Builder
    {
        return self::when($type, function ($query, $type) {
            return match ($type) {
                'text' => $query->whereNotNull('body'),
                'audio' => $query->whereNotNull('voice_url'),
                'video' => $query->whereNotNull('video_url'),
                default => $query,
            };
        })
            ->when($commentableType, function ($query, $commentableType) {
                return $query->where('commentable_type', $commentableType);
            })
            ->with(['user', 'commentable'])
            ->latest();
    }


}
