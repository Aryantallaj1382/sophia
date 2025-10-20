<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHomeWork extends Model
{
    use HasFactory;

    protected $table = 'report_home_work';

    protected $fillable = [
        'title',
        'answer',
        'status',
        'doing_homework',
        'report_registration_id',
        'is_reading',
    ];

    /**
     * رابطه با report_registration
     */
    public function registration()
    {
        return $this->belongsTo(ReportRegistration::class, 'report_registration_id');
    }
    public function getAnswerAttribute($value)
    {
        return $value ? url('public/'.$value) : null;

    }
}
