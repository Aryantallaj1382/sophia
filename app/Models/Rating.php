<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'ratable_type',
        'ratable_id',
        'user_id',
        'rating',
    ];

    /**
     * رابطه مورفیسم (Polymorphic) برای مدل قابل امتیازدهی (مثلاً Professor، Book و ...)
     */
    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * رابطه با کاربر
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

