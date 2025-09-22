<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExamAnswerOption extends Model
{
    protected $fillable = [
        'exam_answer_id',
        'exam_variant_id',
        'exam_variant_option_id'
    ];
    protected $casts = [
        'exam_variant_option_id' => 'integer',
    ];

    public function answer() {
        return $this->belongsTo(ExamAnswer::class);
    }

    public function variant() {
        return $this->belongsTo(ExamVariant::class);
    }

    public function option() {
        return $this->belongsTo(ExamVariantOption::class, 'exam_variant_option_id');
    }
}

