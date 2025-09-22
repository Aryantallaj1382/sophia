<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'plan_type',
        'name',
        'color',
        'price',
        'class_count',
        'original_price',
        'discount_amount',
        'days',
    ];
    protected $casts = [
        'price' => 'int',
    ];
}
