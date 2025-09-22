<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningGoal extends Model
{
    protected $fillable = ['title'];

    public function subgoals()
    {
        return $this->hasMany(LearningSubgoal::class, 'goal_id');
    }
}
