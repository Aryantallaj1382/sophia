<?php

namespace App\Http\Controllers\Api\Professor;

use App\Http\Controllers\Controller;

use App\Models\PrivateProfessorTimeSlot;
use App\Models\ProfessorTimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfessorCalenderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);
        $teacher_id = auth()->user()->professor->id;
        $startDate = $request->start_date;
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));

        $timeSlots = ProfessorTimeSlot::where('professor_id', $teacher_id)
            ->whereDate('date', '>=', today())
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');

        $formattedSlots = $this->formatTimeSlots($timeSlots);

        return api_response($formattedSlots);
    }

    private function formatTimeSlots($timeSlots)
    {
        $daysOfWeek = [
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday',
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
        ];

        $orderedDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $formattedOutput = [];

        foreach ($timeSlots as $date => $slots) {
            $dayOfWeekEnglish = date('l', strtotime($date));
            $dayOfWeek = $daysOfWeek[$dayOfWeekEnglish];

            if (!isset($formattedOutput[$dayOfWeek])) {
                $formattedOutput[$dayOfWeek] = [];
            }

            foreach ($slots as $slot) {
                $formattedOutput[$dayOfWeek][] = [
                    'id' => $slot->id,
                    'time' => substr($slot->time, 0, 5),
                    'status' => $slot->status,
                    'date' => $slot->date,
                ];
            }
        }

        $sortedOutput = [];
        foreach ($orderedDays as $day) {
            if (isset($formattedOutput[$day])) {
                $sortedOutput[$day] = $formattedOutput[$day];
            }
        }

        return $sortedOutput;
    }

    public function show(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);

        $professor_id = auth()->user()->professor->id;
        $startDate = $request->start_date;
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));

        // گرفتن id های رزرو شده از جدول pivot
        $reservedSlotIds = PrivateProfessorTimeSlot::pluck('professor_time_slot_id')->toArray();

        // دریافت تایم‌اسلات‌های آزاد
        $timeSlots = ProfessorTimeSlot::where('professor_id', $professor_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotIn('id', $reservedSlotIds)
            ->select('id', 'date', 'time')
            ->get()
            ->map(function ($slot) {
                $slot->time = substr($slot->time, 0, 5);
                return $slot;
            });


        return api_response([
            'dates' => $timeSlots,
            'min_blocks' => 0
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'dates' => 'required|array',
            'dates.*.date' => 'required|date',
            'dates.*.time' => 'required|date_format:H:i',
            'week' => 'nullable|integer|min:1',
        ]);

        $professor_id = auth()->user()->professor->id;
        $createdSlots = [];
        $failedSlots = [];
        $allSlots = [];
        $weekCount = $request->week ?? 1;

        foreach ($request->dates as $dateItem) {
            for ($i = 0; $i < $weekCount; $i++) {
                $newDate = Carbon::parse($dateItem['date'])->addWeeks($i)->format('Y-m-d');

                $existingSlot = ProfessorTimeSlot::where('professor_id', $professor_id)
                    ->where('date', $newDate)
                    ->where('time', $dateItem['time'])
                    ->first();

                if ($existingSlot) {
                    $allSlots[] = $existingSlot->id;
                    $failedSlots[] = [
                        'date' => $newDate,
                        'time' => $dateItem['time'],
                        'error' => 'Time slot already exists.',
                    ];
                    continue;
                }

                try {
                    $timeSlot = ProfessorTimeSlot::create([
                        'professor_id' => $professor_id,
                        'date' => $newDate,
                        'time' => $dateItem['time'],
                        'status' => 'open',
                    ]);
                    $createdSlots[] = $timeSlot;
                    $allSlots[] = $timeSlot->id; // ذخیره ID تایم جدید در آرایه $allSlots
                } catch (\Exception $e) {
                    $failedSlots[] = [
                        'date' => $newDate,
                        'time' => $dateItem['time'],
                        'error' => $e->getMessage(),
                    ];
                }

            }
        }

        // حذف تایم‌هایی که در دیتابیس هستند ولی در آرایه $allSlots نیستند
        ProfessorTimeSlot::where('professor_id', $professor_id)
            ->whereNotIn('id', $allSlots)
            ->delete();

        return api_response([
            'created' => $createdSlots,
            'failed' => $failedSlots,
        ],'times saved successfully.');
    }


}
