<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamStudentResult extends Model
{
    protected $fillable = [
        'exam_student_id',
        'status',
        'date',
        'score',
        'reading',
        'listening',
        'writing',
        'speaking',
        'file',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * ارتباط با جدول exam_student
     */
    public function examStudent()
    {
        return $this->belongsTo(ExamStudent::class);
    }
}
