<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestionMedia extends Model
{
    protected $fillable = [
        'exam_question_id',
        'path',
        'description',
    ];

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }
    public function getPathAttribute($value)
    {
        return $value? url("storage/$value") : null;

    }
}
