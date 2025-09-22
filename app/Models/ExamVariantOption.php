<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamVariantOption extends Model
{
    use HasFactory;

    protected $table = 'exam_variant_options';

    protected $fillable = [
        'exam_variant_id',
        'text',
        'is_correct'
    ];

    /**
     * هر گزینه متعلق به یک variant است
     */
    public function variant()
    {
        return $this->belongsTo(ExamVariant::class, 'exam_variant_id', 'id');
    }

}
