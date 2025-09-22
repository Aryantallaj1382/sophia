<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = ['user_id', 'message', 'is_seen'];



    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
