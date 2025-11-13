<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorTimeSlot extends Model
{
    protected $table = 'professor_time_slots';


    protected $guarded = [];
    public function privetClassReservations()
    {
        return $this->belongsToMany(
            PrivateClassReservation::class,
            'private_professor_time_slot',
            'professor_time_slot_id',
            'private_class_reservation_id'
        );
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
    public function scopeTimeOfDay($query, $period)
    {
        $ranges = [
            'bamdad' => ['00:00:00', '05:59:59'],
            'sobh'   => ['06:00:00', '11:59:59'],
            'zohr'   => ['12:00:00', '14:59:59'],
            'asr'    => ['15:00:00', '18:59:59'],
            'shab'   => ['19:00:00', '23:59:59'],
        ];

        if (isset($ranges[$period])) {
            [$start, $end] = $ranges[$period];
            return $query->whereBetween('time', [$start, $end]);
        }

        return $query;
    }
}
