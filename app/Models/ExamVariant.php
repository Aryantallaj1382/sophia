<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamVariant extends Model
{
    protected $fillable = [
        'exam_question_id',
        'text',
    ];
    protected $table = 'exam_variants';

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class);
    }
    public function options()
    {
        return $this->hasMany(ExamVariantOption::class, 'exam_variant_id', 'id');
    }

}
