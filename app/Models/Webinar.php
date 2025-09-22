<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'professor_id',
        'age_group_id',
        'language_level_id',
        'subject_id',
        'language_id',
        'platform_id',
        'book_id',
        'min_capacity',
        'max_capacity',
        'date',
        'time',
        'image',
        'class_link',
        'admin_status',
        'view',
    ];
    protected $casts = [
        'date' => 'datetime',
        'time' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return $this->subject->title;
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    protected $appends = ['is_like'];

    public function getIsLikeAttribute()
    {
        $userId = auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function languageLevel()
    {
        return $this->belongsTo(LanguageLevel::class);
    }

    public function subject()
    {
        return $this->belongsTo(LearningSubgoal::class, 'subject_id');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function reservations()
    {
        return $this->hasMany(WebinarReservation::class);
    }
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'ratable');
    }
    public function getRateAttribute()
    {
        if (!auth()->check()) {
            return null;
        }

        return $this->ratings()
            ->where('user_id', auth()->id())
            ->value('rating'); // مستقیم مقدار rating رو برمی‌گردونه یا null
    }
//    public function getTimeAttribute($value)
//    {
//        $schedule = $this->schedules->first();
//
//        if (!$schedule || !$schedule->start_time || !$schedule->end_time) {
//            return null; // یا '' بسته به نیازت
//        }
//
//        return $schedule->start_time->format('H') . '-' . $schedule->end_time->format('H');
//    }
}
