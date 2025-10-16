<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * نام جدول مرتبط با این مدل.
     *
     * @var string
     */
    protected $table = 'blogs';

    /**
     * تعیین فیلدهای قابل پر شدن (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'tags',
        'reading_time',
        'views',
        'type',
        'category',
        'image'
    ];

    /**
     * تبدیل خودکار فیلد‌ها به نوع داده مناسب.
     *
     * @var array
     */
    protected $casts = [
        'tags' => 'array', // تبدیل JSON به آرایه
        'views' => 'integer',
    ];
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
}
