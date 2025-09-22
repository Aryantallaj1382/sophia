<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRegistration extends Model
{
    use HasFactory;

    protected $table = 'report_registration';

    protected $fillable = [
        'absence',
        'absence_time',
        'exam',
        'writing',
        'speaking',
        'reading',
        'listening',
        'vocabulary',
        'final_score',
        'grammar',
        'student_status',
        'exam_solutions',
        'strengths',
        'weaknesses',
        'solutions',
        'score',
        'private_professor_time_slot',
        'class_id',
        'skills',
        'exam_part',
        'exam_name',
    ];

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
