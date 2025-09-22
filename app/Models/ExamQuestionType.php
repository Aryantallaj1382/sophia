<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestionType extends Model
{
    use HasFactory;

    protected $table = 'exam_question_type';

    protected $fillable = [
        'name',
        'exam_question_id',
    ];

    // هر ExamQuestionType متعلق به یک ExamQuestion است
    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id', 'id');
    }
}
