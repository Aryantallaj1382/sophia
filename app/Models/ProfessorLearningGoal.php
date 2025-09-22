<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorLearningGoal extends Model
{
    protected $fillable = ['professor_id', 'language_id', 'subgoal_id'];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }


    public function subgoal()
    {
        return $this->belongsTo(LearningSubgoal::class, 'subgoal_id');
    }
}
