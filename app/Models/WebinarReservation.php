<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'webinar_id',
        'user_id',
        'discount_code',
        'description',
        'status',
    ];

    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
