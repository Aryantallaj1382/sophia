<?php

namespace App\Http\Controllers\Api\Calender;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CalenderController extends Controller
{
    public function getDaySlotsForAll(Request $request)
    {
        $date = $request->input('date'); // مثال: 2025-08-30

        // کش کردن تایم‌های نیم‌ساعته روز (48 تایم)
        $allDayTimes = Cache::rememberForever('half_hour_times', function () {
            $times = [];
            $start = \Carbon\Carbon::parse('00:00');
            $end = \Carbon\Carbon::parse('24:00');

            while ($start < $end) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes(30);

                $times[] = [
                    'time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                ];

                $start->addMinutes(30);
            }

            return $times;
        });

        // گرفتن همه تایم‌اسلات‌های موجود در دیتابیس برای همه اساتید در آن روز
        $slots = \App\Models\ProfessorTimeSlot::where('date', $date)->get()
            ->groupBy('professor_id');

        // اینجا پیجینیت می‌کنیم
        $professors = Professor::paginate(10);

        // تبدیل هر پروفسور به آرایه مناسب
        $professors->getCollection()->transform(function ($professor) use ($slots, $allDayTimes) {
            $professorSlots = $slots->get($professor->id, collect())
                ->keyBy(function($slot){
                    return \Carbon\Carbon::parse($slot->time)->format('H:i');
                });

            // ساخت آرایه اسلات‌ها با وضعیت open/reserved/inactive
            $daySlots = collect($allDayTimes)->map(function($slot) use ($professorSlots) {
                $timeKey = explode(' - ', $slot['time'])[0]; // فقط زمان شروع
                return [
                    'time' => $slot['time'],
                    'status' => $professorSlots->has($timeKey) ? $professorSlots[$timeKey]->status : 'inactive',
                ];
            })->toArray();

            return [
                'professor_id' => $professor->id,
                'professor_name' => $professor->user->name ?? null,
                'profile' => $professor->user->profile ?? null,
                'avg_rate' => (int) ($professor->ratings()->avg('rating') ?? 0),
                'all_rate' => $professor->ratings()->count(),
                'slots' => $daySlots
            ];
        });

        return api_response($professors);
    }
}
