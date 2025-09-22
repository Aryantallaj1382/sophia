<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_question_id',
        'student_id',
        'exam_variant_option_id',
        'text_answer',
        'file',
        'user_id',
    ];

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }


    public function variant() {
        return $this->belongsTo(ExamVariant::class, 'exam_variant_id');
    }

    public function options() {
        return $this->hasMany(ExamAnswerOption::class);
    }

    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getFileAttribute($value)
    {
        return $value ? url('public/'.$value) : null;

    }
}

