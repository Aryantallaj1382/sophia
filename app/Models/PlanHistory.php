<?php

// app/Models/PlanHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanHistory extends Model
{
    protected $fillable = [
        'user_plan_id', 'usable_id', 'usable_type', 'price', 'name'
    ];

    public function userPlan()
    {
        return $this->belongsTo(UserPlan::class);
    }

    public function usable()
    {
        return $this->morphTo();
    }
}

