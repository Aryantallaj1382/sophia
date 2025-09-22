<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * نام جدول مرتبط با این مدل.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * تعیین فیلدهای قابل پر شدن (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'we_chat',
        'birth_date',
        'gender',
        'level',
    ];

    /**
     * تبدیل خودکار فیلد‌ها به نوع داده مناسب.
     *
     * @var array
     */
    protected $casts = [
        'birth_date' => 'date',
    ];
    protected $appends = ['profile'];

    /**
     * رابطه یک به یک با جدول کاربران (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id' );
    }
    public function learningSubgoals()
    {
        return $this->belongsToMany(LearningSubgoal::class, 'student_learning_subgoal');
    }
    public function GetProfileAttribute()
    {
        return  $this->user->profile;

    }
    public function getAgeAttribute(): int
    {
        if (!$this->birth_date) {
            return 0; // اگر تاریخ تولد موجود نبود
        }

        return Carbon::parse($this->birth_date)->age;
    }

    public function getAgeGroupAttribute(): string
    {
        if (!$this->birth_date) {
            return 'نامشخص';
        }

        $age = Carbon::parse($this->birth_date)->age;

        if ($age < 12) {
            return 1;
        } elseif ($age < 18) {
            return 2;
        } else {
            return 3;
        }
    }
}
