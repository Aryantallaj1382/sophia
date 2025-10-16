<?php

namespace App\Http\Controllers\Api\Calender;

use App\Http\Controllers\Controller;
use App\Models\GroupClassReservation;
use App\Models\PrivateClassReservation;
use App\Models\WebinarReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class   PanelCalenderController extends Controller
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
        $student = auth()->user();

        $monthDays = $this->getMonthDays($request->date);

        if (isset($monthDays['error'])) {
            return api_response(null, false, $monthDays['error']);
        }

        $now = Carbon::now()->startOfDay();
        $days = [];

        foreach ($monthDays as $dateStr) {
            $dayClasses = [];

            if ($type === 'all' || $type === 'private') {
                    $privateClasses = PrivateClassReservation::where('user_id', $student->id)
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
                $groupClasses = GroupClassReservation::with('groupClass.schedules')
                    ->where('user_id', $student->id)
                    ->whereHas('groupClass', fn($q) =>
                    $q->whereDate('start_date', '<=', $dateStr)
                        ->whereDate('end_date', '>=', $dateStr)
                    )
                    ->get();

                foreach ($groupClasses as $reservation) {
                    foreach ($reservation->groupClass->schedules as $schedule) {
                        // فقط schedule هایی که متعلق به $dateStr هستند
                        if (Carbon::parse($schedule->date)->toDateString() === $dateStr) {
                            $dayClasses[] = [
                                'type' => 'Group Class',
                                'title' => $reservation->groupClass->name ?? 'No Title',
                                'time' => $schedule->start_time . ' - ' . $schedule->end_time,
                                'class_link' => $reservation->groupClass->class_link,
                            ];
                        }
                    }
                }

            }

            // Webinar
            if ($type === 'all' || $type === 'webinar') {
                $webinars = WebinarReservation::where('user_id', $student->id)
                    ->whereHas('webinar', fn($q) => $q->whereDate('date', $dateStr))
                    ->get();

                foreach ($webinars as $reservation) {
                    $dayClasses[] = [
                        'type' => 'Webinar',
                        'title' => $reservation->webinar->subject->title ?? 'No Title',
                        'time' => $reservation->webinar->start_time . ' - ' . $reservation->webinar->end_time,
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
    public function weeklyCalendar(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'], // Gregorian date in Y-m-d
            'type' => ['nullable', 'in:all,private,group,webinar,workshop'],
        ]);

        $type = $request->get('type', 'all');
        $student = auth()->user()->student;

        $startDate = Carbon::parse($request->date)->startOfWeek(Carbon::SATURDAY); // Start of week (Saturday)

        $classes = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $gregorianDate = $currentDate->toDateString();

            // Group Classes
            if ($type === 'all' || $type === 'group') {
                $groupClasses = GroupClassReservation::with('groupClass.schedules')
                    ->where('user_id', $student->id)
                    ->whereHas('groupClass', fn($q) =>
                    $q->whereDate('start_date', '<=', $gregorianDate)
                        ->whereDate('end_date', '>=', $gregorianDate)
                    )
                    ->get();

                foreach ($groupClasses as $reservation) {
                    foreach ($reservation->groupClass->schedules as $schedule) {
                        if (Carbon::parse($schedule->date)->toDateString() === $gregorianDate) {
                            $classes[] = [
                                'type' => 'Group Class',
                                'title' => $reservation->groupClass->name ?? 'No Title',
                                'time' => $schedule->start_time->format('H:i'),
                                'date' => $schedule->date->format('Y-m-d'),
                                'past' => Carbon::parse($schedule->date)->lt(Carbon::now()),
                                'class_link' => $reservation->groupClass->class_link,
                            ];
                        }
                    }
                }

            }

            if ($type === 'all' || $type === 'private') {
                $privateClasses = PrivateClassReservation::where('user_id', $student->id)
                    ->whereHas('timeSlots', fn($q) => $q->whereDate('date', $gregorianDate))
                    ->with(['timeSlots' => fn($q) => $q->whereDate('date', $gregorianDate)])
                    ->get();

                foreach ($privateClasses as $reservation) {
                    foreach ($reservation->timeSlots as $slot) {
                        $classes[] = [
                            'type' => 'Private Class',
                            'title' => $reservation->subgoal->title ?? 'No Title',
                            'time' => $slot->time,
                            'date' => $gregorianDate,
                            'past' => Carbon::parse($gregorianDate)->lt(Carbon::now()),
                            'class_link' => $reservation->class_link,
                        ];
                    }
                }
            }

            // Webinar
            if ($type === 'all' || $type === 'webinar') {
                $webinars = WebinarReservation::where('user_id', $student->id)
                    ->whereHas('webinar', fn($q) => $q->whereDate('date', $gregorianDate))
                    ->get();

                foreach ($webinars as $reservation) {
                    $classes[] = [
                        'type' => 'Webinar',
                        'title' => $reservation->webinar->subject->title ?? 'No Title',
                        'time' => $reservation->webinar->start_time . ' - ' . $reservation->webinar->end_time,
                        'date' => $gregorianDate,
                        'past' => Carbon::parse($gregorianDate)->lt(Carbon::now()),
                    ];
                }
            }

            }

        return api_response(['data'=>$classes]);
    }
    public function dailyCalendar(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:all,private,group,webinar,workshop'],
        ]);

        $type = $request->get('type', 'all');
        $student = auth()->user()->student;
        $gregorianDate = Carbon::parse($request->date)->toDateString(); // e.g., 2025-05-01

        $classes = [];

        if ($type === 'all' || $type === 'group') {
            $groupClasses = GroupClassReservation::with('groupClass.schedules')
                ->where('user_id', $student->id)
//                ->whereHas('groupClass', fn($q) =>
//                $q->whereDate('start_date', '<=', $gregorianDate)
//                    ->whereDate('end_date', '>=', $gregorianDate)
//                )
                ->get();
            foreach ($groupClasses as $reservation) {
                foreach ($reservation->groupClass->schedules as $schedule) {
                    $classes[] = [
                        'type' => 'Group Class',
                        'title' => $reservation->groupClass->name ?? 'No Title',
                        'time' => $schedule->start_time->format('H:i'),
                        'date' => $schedule->date->format('Y-m-d'),
                        'past' => Carbon::parse($schedule->date)->lt(Carbon::now()),
                        'class_link' => $reservation->groupClass->class_link,
                    ];
                }
            }


        }

        // Private Classes
        if ($type === 'all' || $type === 'private') {
            $privateClasses = PrivateClassReservation::where('user_id', $student->id)
                ->whereHas('timeSlots', fn($q) => $q->whereDate('date', $gregorianDate))
                ->with(['timeSlots' => fn($q) => $q->whereDate('date', $gregorianDate)])
                ->get();

            foreach ($privateClasses as $reservation) {
                foreach ($reservation->timeSlots as $slot) {
                    $classes[] = [
                        'type' => 'Private Class',
                        'title' => $reservation->subgoal->title ?? 'No Title',
                        'time' => $slot->time,
                        'date' => $gregorianDate,
                        'past' => Carbon::parse($gregorianDate)->lt(Carbon::now()),
                        'class_link' => $reservation->class_link,
                    ];
                }
            }
        }

        if ($type === 'all' || $type === 'webinar') {
            $webinars = WebinarReservation::where('user_id', $student->id)
                ->whereHas('webinar', fn($q) => $q->whereDate('date', $gregorianDate))
                ->get();

            foreach ($webinars as $reservation) {
                $classes[] = [
                    'type' => 'Webinar',
                    'title' => $reservation->webinar->subject->title ?? 'No Title',
                    'time' => $reservation->webinar->start_time . ' - ' . $reservation->webinar->end_time,
                    'date' => $gregorianDate,
                    'past' => Carbon::parse($gregorianDate)->lt(Carbon::now()),
                    'teachingType' => 2,
                ];
            }
        }


        return api_response(['data'=>$classes]);
    }

}
