<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'expiration',
        'number_of_attempts',
        'number_of_sections',
        'duration',
        'name',
        'description',
        'view',
        'type', 'age_group_id', 'language_level_id', 'skill_id'

    ];
    protected $casts = [
        'duration' => 'datetime',
    ];

    public function students()
    {
        return $this->hasMany(ExamStudent::class);
    }


    public function parts()
    {
        return $this->hasMany(ExamPart::class);
    }
    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }
    public function book()
    {
        return $this->belongsTo(Book::class, 'books_id');
    }



    public function languageLevel()
    {
        return $this->belongsTo(LanguageLevel::class, 'language_level_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
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
}
