<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPartMedia extends Model
{
    protected $fillable = [
        'exam_part_id',
        'path',
        'description',
    ];
    protected $table = 'exam_part_media';

    public function part()
    {
        return $this->belongsTo(ExamPart::class, 'exam_part_id');
    }
    public function getPathAttribute($value)
    {
        return $value? url("storage/$value") : null;

    }
}
