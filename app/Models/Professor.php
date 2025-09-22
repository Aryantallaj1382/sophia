<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $guarded = [];
    public function learningGoals()
    {
        return $this->hasMany(ProfessorLearningGoal::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_professor');
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'ratable');
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function timeSlots()
    {
        return $this->hasMany(ProfessorTimeSlot::class, 'professor_id');
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function accents()
    {
        return $this->belongsToMany(Accent::class, 'professor_accent');
    }

    public function ageGroups()
    {
        return $this->belongsToMany(AgeGroup::class,'professor_age_group');
    }

    public function languageLevels()
    {
        return $this->belongsToMany(LanguageLevel::class,'professor_language_level');
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class,'professor_platform');
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
    public function getRateAttribute()
    {
        if (!auth()->check()) {
            return null;
        }

        return $this->ratings()
            ->where('user_id', auth()->id())
            ->value('rating');
    }
    public function scopeHasTimeOfDay($query, $period)
    {
        return $query->whereHas('timeSlots', function ($q) use ($period) {
            $q->timeOfDay($period);
        });
    }
    // app/Models/Professor.php
    public function getNearestOpenTimeAttribute()
    {
        $slot = $this->timeSlots()
            ->where('status', 'open')
            ->where(function($q) {
                $q->where('date', '>', now()->toDateString())
                    ->orWhere(function($q2) {
                        $q2->where('date', now()->toDateString())
                            ->where('time', '>=', now()->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->first();

        if (!$slot) {
            return null;
        }

        $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $slot->date . ' ' . $slot->time);

        return $dateTime->format('D g A');
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'professor_skill');
    }

}
