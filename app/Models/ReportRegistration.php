<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRegistration extends Model
{
    use HasFactory;

    protected $table = 'report_registration';

    protected $guarded = [];

    protected $casts = [
        'exam' => 'boolean',
        'writing' => 'int',
        'speaking' => 'int',
        'reading' => 'int',
        'listening' => 'int',
        'vocabulary' => 'int',
        'grammar' => 'int',
        'exam_solutions' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'solutions' => 'array',
        'score' => 'array',
        'skills' => 'array',
        'exam_part' => 'array',
    ];

    public function timeSlot()
    {
        return $this->belongsTo(PrivateProfessorTimeSlot::class, 'private_professor_time_slot');
    }

    /**
     * رابطه با private_class_reservations
     */
    public function classReservation()
    {
        return $this->belongsTo(PrivateClassReservation::class, 'class_id');
    }

    /**
     * رابطه با report_home_work ها
     */
    public function homeWorks()
    {
        return $this->hasMany(ReportHomeWork::class, 'report_registration_id');
    }
}
