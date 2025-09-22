<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_part_id',
        'exam_id',
        'question_type',
        'title',
        'description',
        'question',
        'number',
    ];
    protected $table = 'exam_questions';

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function types()
    {
        return $this->hasMany(ExamQuestionType::class, 'exam_question_id', 'id');
    }
    public function part()
    {
        return $this->belongsTo(ExamPart::class, 'exam_part_id');
    }

    public function media()
    {
        return $this->hasMany(ExamQuestionMedia::class);
    }

    public function variants()
    {
        return $this->hasMany(ExamVariant::class , 'exam_question_id');
    }
}
