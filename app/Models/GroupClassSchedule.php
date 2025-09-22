<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupClassSchedule extends Model
{
    use HasFactory;
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'date' => 'date',
    ];
    protected $fillable = [
        'group_class_id',
        'day',
        'start_time',
        'end_time',
        'date',
    ];

    // روابط
    public function groupClass()
    {
        return $this->belongsTo(GroupClass::class);
    }
    public function getStatusAttribute()
    {
        $today = Carbon::today();

        if ($this->date->isSameDay($today)) {
            return 'Today';
        } elseif ($this->date->lt($today)) {
            return 'Finished';
        } else {
            return 'Upcoming';
        }
    }
    public function getSessionNumberAttribute()
    {
        $sessions = self::whereRelation('groupClass','group_class_id', $this->group_class_id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->pluck('id')
            ->toArray();

        return array_search($this->id, $sessions) + 1; // +1 چون اندیس آرایه از صفر شروع میشه
    }

    public static function latestSession($groupClassId): ?GroupClassSchedule
    {
        $today = Carbon::today();

        // اول جلسه آینده
        $upcoming = self::where('group_class_id', $groupClassId)
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        if ($upcoming) {
            return $upcoming;
        }

        // اگر جلسه آینده نبود → آخرین جلسه گذشته
        return self::where('group_class_id', $groupClassId)
            ->whereDate('date', '<', $today)
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->first();
    }


    // اکسسور برای تاریخ جلسه
    public function getLatestDateAttribute()
    {
        $latest = self::latestSession($this->group_class_id);
        return $latest ? $latest->date->format('Y-m-d') : null;
    }

    // اکسسور برای زمان جلسه
    public function getLatestTimeAttribute()
    {
        $latest = self::latestSession($this->group_class_id);
        return $latest ? $latest->start_time->format('H:i') : null;
    }

}
