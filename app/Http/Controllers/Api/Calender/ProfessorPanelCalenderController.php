<?php

namespace App\Http\Controllers\Api\Calender;

use App\Http\Controllers\Controller;
use App\Models\GroupClass;
use App\Models\GroupClassReservation;
use App\Models\PrivateClassReservation;
use App\Models\Webinar;
use App\Models\WebinarReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProfessorPanelCalenderController extends Controller
{
    public function getMonthDays($gregorianDate)
    {
        try {
            $date = Carbon::parse($gregorianDate);
            $year = $date->year;
            $month = $date->month;

            // تعداد روزهای ماه
            $daysInMonth = $date->daysInMonth;

            // ساخت آرایه روزهای ماه به فرمت Y-m-d
            $days = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $days[] = Carbon::create($year, $month, $day)->toDateString();
            }

            return $days;
        } catch (\Exception $e) {
            return ['error' => 'Invalid date format or processing error: ' . $e->getMessage()];
        }
    }

    public function monthlyCalendar(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:all,private,group,webinar,workshop'],
        ]);

        $type = $request->get('type', 'all');
        $professor = auth()->user();

        $monthDays = $this->getMonthDays($request->date);

        if (isset($monthDays['error'])) {
            return api_response(null, false, $monthDays['error']);
        }

        $now = Carbon::now()->startOfDay();
        $days = [];

        foreach ($monthDays as $dateStr) {
            $dayClasses = [];

            if ($type === 'all' || $type === 'private') {
                $privateClasses = PrivateClassReservation::where('professor_id', $professor->id)
                    ->whereHas('timeSlots', fn($q) => $q->whereDate('date', $dateStr))
                    ->with(['timeSlots' => fn($q) => $q->whereDate('date', $dateStr)])
                    ->get();

                foreach ($privateClasses as $reservation) {
                    foreach ($reservation->timeSlots as $slot) {
                        $dayClasses[] = [
                            'type' => 'Private Class',
                            'title' => $reservation->subgoal->title ?? 'No Title',
                            'time' => $slot->time,
                            'past' => Carbon::parse($dateStr)->lt($now),
                            'class_link' => $reservation->class_link,
                        ];
                    }
                }
            }

            if ($type === 'all' || $type === 'group') {
                $groupClasses = GroupClass::with('schedules')
                    ->where('professor_id', $professor->id)
                        ->whereDate('start_date', '<=', $dateStr)
                        ->whereDate('end_date', '>=', $dateStr)

                    ->get();

                foreach ($groupClasses as $reservation) {
                    foreach ($reservation->schedules as $schedule) {
                        if (Carbon::parse($schedule->date)->toDateString() === $dateStr) {
                            $dayClasses[] = [
                                'type' => 'Group Class',
                                'title' => $reservation->name ?? 'No Title',
                                'time' => $schedule->start_time . ' - ' . $schedule->end_time,
                                'class_link' => $reservation->class_link,
                            ];
                        }
                    }
                }

            }

            // Webinar
            if ($type === 'all' || $type === 'webinar') {
                $webinars = Webinar::where('professor_id', $professor->id)
                     ->whereDate('date', $dateStr)
                    ->get();

                foreach ($webinars as $reservation) {
                    $dayClasses[] = [
                        'type' => 'Webinar',
                        'title' => $reservation->subject->title ?? 'No Title',
                        'time' => $reservation->start_time . ' - ' . $reservation->end_time,
                    ];
                }
            }
            $days[] = [
                'date' => $dateStr,
                'past' => Carbon::parse($dateStr)->lt($now),
                'classes' => $dayClasses,
            ];
        }

        return api_response(['data'=>$days]);
    }

}
