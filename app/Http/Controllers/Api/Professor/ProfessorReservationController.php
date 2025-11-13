<?php

namespace App\Http\Controllers\Api\Professor;

use App\Models\ProfessorTimeSlot;
use Illuminate\Http\Request;

class ProfessorReservationController
{
    public function index2(Request $request)
    {
        $timeSlots = ProfessorTimeSlot::withCount('privetClassReservations')->get();

        $periods = $this->definePeriods();

        $formattedSlots = $this->formatSlotsWithReservations($timeSlots, $periods);

        $dailyReservations = $this->calculateWeeklyReservations($timeSlots);

        return api_response([
            'periods' => $formattedSlots,
            'daily_reservations' => $dailyReservations,
        ]);
    }

    private function formatSlotsWithReservations($timeSlots, $periods)
    {
        $formattedOutput = [];
        $maxReservations = 0;

        $tempReservations = [];

        foreach ($periods as $period => $times) {
            foreach ($times as $time) {
                $isReserved = false;
                foreach ($timeSlots as $slot) {
                    if (substr($slot->time, 0, 5) == $time) {
                        $tempReservations[$period][$time] = $slot->reservations_count;
                        $maxReservations = max($maxReservations, $slot->reservations_count);
                        $isReserved = true;
                        break;
                    }
                }
                if (!$isReserved) {
                    $tempReservations[$period][$time] = 0;
                }
            }
        }

        if ($maxReservations > 0) {
            foreach ($tempReservations as $period => $times) {
                foreach ($times as $time => $count) {
                    $formattedOutput[$period][$time] = round(($count / $maxReservations) * 5);
                }
            }
        } else {
            $formattedOutput = $tempReservations;
        }

        return $formattedOutput;
    }

    private function calculateWeeklyReservations($timeSlots)
    {
        $dailyReservations = [
            'Saturday' => 0,
            'Sunday' => 0,
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
        ];

        foreach ($timeSlots as $slot) {
            $dayOfWeek = date('l', strtotime($slot->date));
            $dailyReservations[$dayOfWeek] += $slot->reservations_count;
        }

        $maxReservations = max($dailyReservations);

        if ($maxReservations > 0) {
            foreach ($dailyReservations as $day => $count) {
                $dailyReservations[$day] = round(($count / $maxReservations) * 5);
            }
        }

        return $dailyReservations;
    }

    private function definePeriods()
    {
        return [
            'Midnight' => [
                '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30'
            ],
            'Morning' => [
                '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30'
            ],
            'Afternoon' => [
                '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
            ],
            'Evening' => [
                '18:00', '18:30', '19:00', '19:30', '20:00', '20:30'
            ],
            'Night' => [
                '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
            ],
        ];
    }


}
