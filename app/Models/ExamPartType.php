<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPartType extends Model
{
    protected $fillable = ['name'];

    public function parts()
    {
        return $this->hasMany(ExamPart::class);
    }
}
