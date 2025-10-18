<?php

namespace App\Http\Controllers\Api\Professor;

use App\Http\Controllers\Controller;
use App\Models\GroupClass;
use App\Models\GroupClassReservation;
use App\Models\PrivateClassReservation;
use App\Models\PrivateProfessorTimeSlot;
use App\Models\Story;
use App\Models\Webinar;
use App\Models\WebinarReservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $professor = auth()->user()->professor;
        $trial = PrivateClassReservation::where('professor_id' , $professor->id)->where('class_type' ,'trial')->count();
        $sessional = PrivateClassReservation::where('professor_id' , $professor->id)->where('class_type' ,'sessional')->count();
        $placement = PrivateClassReservation::where('professor_id' , $professor->id)->where('class_type' ,'placement')->count();
        $group = GroupClass::where('professor_id' , $professor->id)->count();
        $webinar = Webinar::where('professor_id' , $professor->id)->count();
        $story = Story::where('professor_id' , $professor->id)->get();

        $private = PrivateProfessorTimeSlot::whereRelation('reservation','professor_id' , $professor->id)->whereRelation('reservation','status' ,'!=', 'pending')
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
        $group_reserve = GroupClassReservation::whereRelation('groupClass' , 'professor_id' , $professor->id)
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
        $webinar_reserve = WebinarReservation::whereRelation('webinar','professor_id', $professor->id)->where('status' ,'!=', 'pending')
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
            'trial' => $trial,
            'placement' => $placement,
            'sessional' => $sessional,
            'group' => $group,
            'webinar' => $webinar,
            'story' => $story,
            'private' => $private,
            'webinar_reserve' => $webinar_reserve,
            'group_reserve' => $group_reserve,

            ]);


    }


}
