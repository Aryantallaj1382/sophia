<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamStudent extends Model
{
    use HasFactory;

    protected $table = 'exam_student';

    protected $fillable = [
        'exam_id',
        'student_id',
        'status',
        'score',
        'started_at',
        'finished_at',
        'expired_at',
        'created_at',

    ];

    protected $casts = [
         'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'expired_at' => 'datetime',
    ];



    public function getStatusFaAttribute()
    {
        $statuses = [
            'completed' => 'تمام شده',
            'in_progress' => 'در حال ازمون',
            'not_started' =>'  شروع نکرده ',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // رابطه با دانشجو
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function results()
    {
        return $this->hasMany(ExamStudentResult::class);
    }
}
