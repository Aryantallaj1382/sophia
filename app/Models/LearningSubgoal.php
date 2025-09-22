<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningSubgoal extends Model
{
    protected $fillable = ['goal_id', 'title', 'sub' , 'title_ch'];

    public function goal()
    {
        return $this->belongsTo(LearningGoal::class, 'goal_id');
    }

    public function professorLearningGoals()
    {
        return $this->hasMany(ProfessorLearningGoal::class, 'subgoal_id');
    }
}
