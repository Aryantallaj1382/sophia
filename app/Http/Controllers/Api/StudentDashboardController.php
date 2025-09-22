<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamStudent;
use App\Models\GroupClassReservation;
use App\Models\GroupClassSchedule;
use App\Models\PrivateClassReservation;
use App\Models\PrivateProfessorTimeSlot;
use App\Models\UserPlan;
use App\Models\WebinarReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->id;
        $myPlans = UserPlan::where('user_id' , $user)->whereDate('expires_at' , '>=', now())->where('class_count' , '>', 0)->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->plan->id,
                    'name' => $plan->plan->name,
                    'plan_type' => $plan->plan->plan_type,
                ];
            });

        $myPlacement = ExamStudent::where('student_id' , $user)->whereRelation('exam', 'type' ,'placement')->count();
        $myFinal = ExamStudent::where('student_id' , $user)->whereRelation('exam', 'type' ,'final')->count();
        $myMock = ExamStudent::where('student_id' , $user)->whereRelation('exam', 'type' ,'mock')->count();

        $myPrivate = PrivateClassReservation::where('user_id' , $user)->where('status' ,'!=', 'pending')->count();
        $myWebinar = WebinarReservation::where('user_id' , $user)->where('status' ,'!=', 'pending')->count();
        $myGroup = GroupClassReservation::where('user_id' , $user)->where('status' ,'!=', 'pending')->count();


        $private = PrivateProfessorTimeSlot::whereRelation('reservation','user_id' , $user)->whereRelation('reservation','status' ,'!=', 'pending')
            ->whereDate('date', '>', now())->oldest()->take(3)->get()->map(function ($slot) {
                return [
                    'id' => $slot->reservation->professor->id,
                    'title' => $slot->reservation->professor->name,
                    'image' => $slot->reservation->professor->user->profile,
                    'subgoal' => $slot->reservation?->subgoal?->title ?? null,
                    'date' => $slot?->date?->format('Y-m-d'),
                    'time' => $slot->time?->format('H:i'),
                    'session_number' => $slot->session_number,
                    'all_session' => $slot->reservation->sessions_count,
                    'class_link' =>$slot->reservation->class_link,
                ];
            });

        $group_reserve = GroupClassReservation::where('user_id', $user)
            ->where('status', '!=', 'pending')
            ->with(['groupClass.schedules', 'groupClass.subject', 'groupClass.professor'])
            ->get()
            ->pluck('groupClass')
            ->flatten()
            ->map(function ($group) {
                $sortedSchedules = $group->schedules->sortBy('date')->values();
                $nearestSchedule = $sortedSchedules
                    ->where('date', '>=', Carbon::now())
                    ->first();

                if ($nearestSchedule) {
                    $sessionIndex = $sortedSchedules->search(function ($item) use ($nearestSchedule) {
                        return $item->id === $nearestSchedule->id;
                    });

                    return [
                        'id'           => $group->id,
                        'image'        => $group->image,
                        'title'        => $group->subject->title ?? null,
                        'professor'    => $group->professor->name ?? null,
                        'profile'    => $group->professor->user->profile ?? null,
                        'date'         => $nearestSchedule->date?->format('Y-m-d'),
                        'time'         => $nearestSchedule->start_time?->format('H:i'),
                        'all_session'  => $sortedSchedules->count(),
                        'class_link' =>null,

                        'session_number'   => $sessionIndex !== false ? $sessionIndex + 1 : null, // شماره جلسه
                    ];
                }
                return null;
            })
            ->filter()
            ->sortBy('date')
            ->take(3)
            ->values();


        $webinar_reserve = WebinarReservation::where('user_id', $user)->where('status' ,'!=', 'pending')
            ->whereRelation('webinar','date','>' ,now())->oldest()->take(3)->get()->map(function ($webinar) {
            return [
                'id' => $webinar->id,
                'title' => $webinar->webinar->subject->title ?? null,
                'image' => $webinar->webinar->image?? null,
                'date' => $webinar->webinar->date?->format('Y-m-d')?? null,
                'time' => $webinar->webinar->time?->format('H:i')?? null,
                'professor' => $webinar->webinar->professor->name?? null,
                'profile' => $webinar->webinar->professor->user->profile?? null,
                'class_link' =>null,

            ];
        });

        return api_response([
            'my_plans' => $myPlans,
            'myPlacement' => $myPlacement,
            'myFinal' => $myFinal,
            'myMock' => $myMock,
            'myPrivate' => $myPrivate,
            'myWebinar' => $myWebinar,
            'myGroup' => $myGroup,
            'private_reserve' => $private,
            "group_reserve" => $group_reserve,
            "webinar_reserve" => $webinar_reserve,

        ]);

    }
    public function home_work(Request $request)
    {
        $user = auth()->user();

        $perPage = $request->input('per_page', 10); // تعداد موارد در هر صفحه، پیش‌فرض 10

        // دریافت همه تکالیف کلاس‌های کاربر
        $homeWorks = \App\Models\ReportHomeWork::whereHas('registration.timeSlot', function ($q) use ($user) {
            $q->whereHas('reservation', function ($q2) use ($user) {
                $q2->where('user_id', $user->id);
            });
        })
            ->with(['registration.timeSlot'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = $homeWorks->through(function ($homeWork) {
            return [
                'id' => $homeWork->id,
                'name' => $homeWork->registration->classReservation->subgoal->title,
                'professor' => $homeWork->registration->classReservation->professor->name,
                'title' => $homeWork->title,
                'answer' => $homeWork->answer,
                'is_reading' => (int) $homeWork->is_reading,
                'status' => $homeWork->status,
                'session_number' => $homeWork->registration->timeSlot->session_number ?? null,
                'class_id' => $homeWork->registration->timeSlot->reservation->id ?? null,
            ];
        });

        return api_response($data->toArray());
    }

}
