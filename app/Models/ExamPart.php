<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPart extends Model
{
    protected $fillable = [
        'exam_id',
        'exam_part_type_id',
        'title',
        'text',
        'passenger',
        'passenger_title',
        'question_title',
        'number',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function type()
    {
        return $this->belongsTo(ExamPartType::class, 'exam_part_type_id');
    }

    public function media()
    {
        return $this->hasMany(ExamPartMedia::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }
}

