<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorPlacement extends Model
{

    protected $guarded=[];

    // روابط
    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function languageLevel()
    {
        return $this->belongsTo(LanguageLevel::class);
    }

}
