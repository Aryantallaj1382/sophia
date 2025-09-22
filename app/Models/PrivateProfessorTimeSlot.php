<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateProfessorTimeSlot extends Model
{
    protected $table = 'private_professor_time_slot';
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    protected $guarded = [];

    public function reservation()
    {
        return $this->belongsTo(PrivateClassReservation::class, 'private_class_reservation_id');
    }

    public function professorTimeSlot()
    {
        return $this->belongsTo(ProfessorTimeSlot::class, 'professor_time_slot_id');
    }
    public function registrations()
    {
        return $this->hasMany(ReportRegistration::class, 'private_professor_time_slot');
    }
    public function homeWorks()
    {
        return $this->hasManyThrough(
            ReportHomeWork::class,
            ReportRegistration::class,
            'private_professor_time_slot',
            'report_registration_id',
            'id',
            'id'
        );
    }
    public function getSessionNumberAttribute()
    {
        $sessions = self::where('private_class_reservation_id', $this->private_class_reservation_id)
            ->orderBy('date')
            ->orderBy('time')
            ->pluck('id')
            ->toArray();

        return array_search($this->id, $sessions) + 1; // +1 چون اندیس آرایه از صفر شروع میشه
    }

}
