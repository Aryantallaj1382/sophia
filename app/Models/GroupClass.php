<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupClass extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function reservations()
    {
        return $this->hasMany(GroupClassReservation::class);
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

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function level()
    {
        return $this->belongsTo(LanguageLevel::class , 'language_level_id');
    }

    public function subject()
    {
        return $this->belongsTo(LearningSubgoal::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function schedules()
    {
        return $this->hasMany(GroupClassSchedule::class);
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
    public function getNameAttribute()
    {
        return $this->subject->title;
    }
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
    public function getTimeAttribute($value)
    {
        $schedule = $this->schedules->first();

        if (!$schedule || !$schedule->start_time || !$schedule->end_time) {
            return null; // یا '' بسته به نیازت
        }

        return $schedule->start_time->format('H') . '-' . $schedule->end_time->format('H');
    }

}
