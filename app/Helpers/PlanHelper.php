<?php

namespace App\Helpers;

use App\Http\Controllers\Api\Exam\ExamController;
use App\Models\Exam;
use App\Models\ExamStudent;
use App\Models\PlanHistory;
use App\Models\UserPlan;
use App\Models\PrivateClassReservation;
use App\Models\GroupClassReservation;
use App\Models\WebinarReservation;
use Carbon\Carbon;

class PlanHelper
{
    public static function reserveClass($userId, $classType, $request , $id )
    {
        $userPlan = UserPlan::where('user_id', $userId)
            ->where('is_active', true)
            ->whereRelation('plan', 'plan_type', $classType)
            ->where('expires_at', '>=', Carbon::now())
            ->first();


            if (!$userPlan) {
            return ['success' => false, 'message' => 'The user does not have an active plan.'];
        }

        if ($userPlan->plan->plan_type !== $classType) {
            return ['success' => false, 'message' => 'The plan type does not match the class.'];
        }

        if ($userPlan->class_count <= 0) {
            return ['success' => false, 'message' => 'There are no remaining classes in the plan.'];
        }

        switch ($classType) {
            case 'one_to_one':
                $reservation = PrivateClassReservation::find($id);
                $reservation->update(['status' => 'confirmed']);
                break;

            case 'group':
                $reservation = GroupClassReservation::create([
                    'group_class_id' => $id,
                    'user_id' => $userId,
                    'discount_code' => $request->discount_code ?? null,
                    'description' => $request->description ?? null,
                    'status' => 'approved',
                ]);
                break;

            case 'webinar':
                $reservation = WebinarReservation::create([
                    'webinar_id' => $id,
                    'user_id' => $userId,
                    'discount_code' => $request->discount_code ?? null,
                    'description' => $request->description ?? null,
                    'status' => 'approved',
                ]);
            case 'mock_test':
                $exam = Exam::find($id);
                $hours = (int) $exam->duration->format('H'); // ساعت
                $minutes = (int) $exam->duration->format('i'); // دقیقه
                $reservation = ExamStudent::create([
                    'exam_id' => $id,
                    'user_id' => $userId,
                    'status' => 'not_started',
                    'student_id'=>$userId,
//                    'expired_at' => Carbon::now()->addHours($hours)->addMinutes($minutes),
//                    'started_at' => Carbon::now(),

                ]);
                break;

            default:
                return ['success' => false, 'message' => 'نوع کلاس نامعتبر است'];
        }

        $userPlan->decrement('class_count');
        PlanHistory::create([
            'user_plan_id' => $userPlan->id,
            'usable_id'    => $reservation->id,
            'usable_type'  => get_class($reservation),
            'price'        => $userPlan->plan->price,
            'name'         => $userPlan->plan->name,
        ]);
        return ['success' => true, 'reservation' => $reservation];
    }
}
