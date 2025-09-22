<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'started_at',
        'expires_at',
        'class_count',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'date',
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function getExpiryAttribute()
    {
        if (!$this->end_date) {
            return null;
        }

        $now = Carbon::now();
        $end = Carbon::parse($this->end_date);

        // تعداد روز باقی‌مانده
        return $now->diffInDays($end, false); // false برای برگشت منفی اگر گذشته باشد
    }
}
