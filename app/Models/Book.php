<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    protected $casts = [
        'topics' => 'array',
    ];

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'book_professor');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getImageAttribute($value)
    {
        return $value ? url('public/' . $value) : null;
    }
    public function getFileAttribute($value)
    {
        return $value ? url('public/' . $value) : null;
    }
    public function getVideoAttribute($value)
    {
        return $value ? url('public/' . $value) : null;
    }

    public function ageGroups()
    {
        return $this->belongsToMany(AgeGroup::class, 'book_age_group');
    }

    public function languageLevels()
    {
        return $this->belongsToMany(LanguageLevel::class, 'book_language_level');
    }

    public function samplePages()
    {
        return $this->hasMany(BookSamplePage::class);
    }

    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case 'most_viewed':
                    $query->orderBy('view_count', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filters['author'])) {
            $author = $filters['author'];
            $query->where('author', 'like', "%{$author}%");
        }

        if (!empty($filters['book_type'])) {
            $book_type = $filters['book_type'];
            $query->where('book_type', 'like', "%{$book_type}%");
        }

        if (!empty($filters['volumes'])) {
            $volumeFilter = (int)$filters['volumes'];
            if ($volumeFilter === 1) {
                $query->where('volume', 1);
            } elseif ($volumeFilter === 2) {
                $query->where('volume', '>', 1);
            }
        }

        if (!empty($filters['accents'])) {
            $accentId = $filters['accents'];
            $query->whereHas('accents', function ($q) use ($accentId) {
                $q->where('accents.id', $accentId);
            });
        }

        if (!empty($filters['edition'])) {
            $edition = $filters['edition'];
            $query->where('edition', $edition);
        }

        if (!empty($filters['title_file'])) {
            $title_file = $filters['title_file'];
            $query->where('title_file', $title_file);
        }

        if (!empty($filters['language_levels'])) {
            $languageLevelId = $filters['language_levels'];
            $query->whereHas('languageLevels', function ($q) use ($languageLevelId) {
                $q->where('language_levels.id', $languageLevelId);
            });
        }

        if (!empty($filters['learning_goals'])) {
            $learningGoalId = $filters['learning_goals'];
            $query->whereHas('learningGoals', function ($q) use ($learningGoalId) {
                $q->where('professor_learning_goals.id', $learningGoalId);
            });
        }

        if (!empty($filters['age_groups'])) {
            $ageGroupId = $filters['age_groups'];
            $query->whereHas('ageGroups', function ($q) use ($ageGroupId) {
                $q->where('age_groups.id', $ageGroupId);
            });
        }

        return $query;
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
