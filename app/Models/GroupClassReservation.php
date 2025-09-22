<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupClassReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_class_id',
        'user_id',
        'discount_code',
        'description',
        'status',
    ];

    public function groupClass()
    {
        return $this->belongsTo(GroupClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
