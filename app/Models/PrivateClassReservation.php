<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateClassReservation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }
// داخل مدل PrivateClassReservation

    public function reservedBooks()
    {
        return $this->hasMany(ReservedBook::class, 'private_class_reservation_id');
    }

    public function languageLevel()
    {
        return $this->belongsTo(LanguageLevel::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function subgoal()
    {
        return $this->belongsTo(LearningSubgoal::class, 'subgoal_id');
    }

    public function timeSlots()
    {
        return $this->hasMany(PrivateProfessorTimeSlot::class, 'private_class_reservation_id');
    }
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
    public function getLinkAttribute()
    {
        if ($this->platform?->title == 'Class In')
        {
            return $this->professor->classin_link;
        }
        elseif ($this->platform?->title == 'Zoom')
        {
            return $this->professor->zoom_link;
        }
        elseif ($this->platform?->title == 'Voov')
        {
            return $this->professor->voov_link;
        }
        else return null;

    }
}
