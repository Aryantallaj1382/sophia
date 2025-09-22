<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\contact\Price;
use App\Models\GroupClassReservation;
use App\Models\GroupClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupClassController extends Controller
{

    public function index()
    {

        $user = auth()->user();
        $gruop = GroupClassReservation::where('user_id', $user->id)->paginate();
        $gruop->getCollection()->transform(function ($item, $key) {
            $latestSchedule = GroupClassSchedule::latestSession($item->groupClass->id);

            return [
                'id' => $item->id,
                'image' => $item->groupClass->image,
                'professor_id' => $item->groupClass->professor_id,
                'professor_name' => $item->groupClass->professor->name,
                'professor_profile' => $item->groupClass->professor->user->profile,
                'subgoal' => $item->groupClass->subject->goal->title. ' ('. $item->groupClass->subject->title.')',
                'date' => $latestSchedule ? $latestSchedule->date->format('Y-m-d') : null,
                'time' => $latestSchedule ? $latestSchedule->start_time->format('H:i') : null,
                'count' => $item->groupClass->schedules->count(),
                'session_number' => $item->groupClass->sessions_count, // اضافه شد
            ];
        });

        return api_response($gruop);
    }
    public function new_class($id)
    {
        $class = GroupClassReservation::find($id);
        $group = $class->group_class_id;

        $futureSessions = GroupClassSchedule::where('group_class_id', $group)
            ->whereDate('date', '>', now())
            ->get();
        $session = $futureSessions->first();
        $session_count = GroupClassSchedule::where('group_class_id', $group)->count();
        $certificate = Certificate::where('for' , 'group')->where('for_id', $id)->exists();
        $certificate_file = Certificate::where('for' , 'group')->where('for_id', $id)->first();
        if ( $futureSessions->isEmpty())
        {
            $class->update(['status' => 'completed']);
        }
        $a = [
            'link' => $class->groupClass->class_link ?? null,
            'professor_id' => $class->groupClass->professor->user->id ?? null,
            'profile' => $class->groupClass->professor->user->profile ?? null,
            'subgoal' => $class->groupClass->subject->goal->title . ' (' . $class->groupClass->subject->title . ')',
            'class' => $session ? $session->session_number . ' from ' . $session_count : null,
            'session_id' => $session ? $session->id : null,
            'date' => $session ? $session->date->format('j F') : null,
            'time' => $session ? $session->start_time->format('H:i') : null,
            'is_finished' => $futureSessions->isEmpty(),
            'has_certificate' => $certificate,
            'certificate_file' => $certificate_file->file ?? null,
        ];

        return api_response($a);
    }


    public function information($id)
    {
        $group = GroupClassReservation::find($id);
        $return = [
            'id' => $group->id,
            'professor_name' => $group->groupClass->professor->name,
            'professor_id' => $group->groupClass->professor->id,
            'count' => $group->groupClass->schedules->count(),
            'ageGroup' => $group->groupClass->ageGroup->title,
            'languageLevel' => $group->groupClass->level->title,
            'platform' => $group->groupClass->platform->title,
            'description' => $group->description,
            'subgoal' => $group->groupClass->subject->goal->title . ' (' . $group->groupClass->subject->title . ')',
            'book' => [
                'name' => $group->groupClass?->book?->name,
                'languageLevel' => $group->groupClass?->level->title,
                'ageGroup' => $group->groupClass?->ageGroup->title,
                'image' => $group->groupClass?->book?->image,
                'author' => $group->groupClass?->book->author,
                'edition' => $group->groupClass?->book->edition,
                'topics' => $group->groupClass?->book->topics,
            ],
            'reserve_time' => $group->created_at->format('D ,j M Y , H:i'),

        ];
        return api_response($return);
    }

    public function sessions(Request $request, $id)
    {
        $upcoming = $request->boolean('upcoming', false); // مقدار پیش‌فرض false
        $private = GroupClassReservation::find($id);
        $group = $private->groupClass;

        if (!$private) {
            return [];
        }
        $link = $group->class_link;
        $professor_id = $group->professor->user->id;

        $timeSlots = $group->schedules;

        if ($upcoming) {
            $timeSlots = $timeSlots->filter(function ($slot) {
                return $slot->status === 'upcoming' || $slot->status === 'today';
            });
        }

        return $timeSlots
            ->sortBy('session_number')
            ->values()
            ->map(function ($item) use ($link, $professor_id) {
                return [
                    'id' => $item->id,
                    'session_number' => $item->session_number,
                    'date' => $item->date->format('D ,j M Y'),
                    'time' => $item->start_time->format('H:i'),
                    'origin_date' => $item->date->copy()->setTimeFrom($item->start_time), // تاریخ و ساعت با هم
                    'status' => $item->status,
                    'class_link' => $link,
                    'professor_id' => $professor_id,
                ];
            })
            ->toArray();
    }



    public function calender($id)
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, 'Unauthorized', 401);
        }
        $private = GroupClassReservation::find($id);
        $group = $private->groupClass;
        if (!$private) {
            return api_response(null, 'Reservation not found', 404);
        }
        $timeSlots = $group->schedules()->get();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $calendar = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $daySessions = $timeSlots->filter(function ($slot) use ($date) {
                return $slot->date->isSameDay($date);
            })->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'origin_date' => $slot->date->copy()->setTimeFrom($slot->start_time)->toDateTimeString(),
                    'date' => $slot->date->format('l'),
                    'SessionNumber' => $slot->session_number,
                    'status' => $slot->status,
                    'class_link' => $slot->groupClass->class_link,
                    'time' => $slot->start_time->format('H:i'),
                ];
            })->values();
            $calendar[] = [
                'day' => $date->format('Y-m-d'),
                'sessions' => $daySessions,
            ];
        }

        return api_response(['data' => $calendar]);
    }


    public function certificate(Request $request,$id)
    {
        $request->validate([
            'en_name' => 'nullable|string',
            'zh_name' => 'nullable|string',
            'in_person' => 'nullable',
            'electronic' => 'nullable',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);
        if ( $request->electronic == 1 ) {
            $type = 'electronic';
        }
        else{
            $type = 'printable';

        }
        $class = GroupClassReservation::find($id);
        if($class->status != 'completed')
        {
            return api_response([], 'your class has not completed', 422);
        }

        Certificate::updateOrCreate
        ([
                'for_id' => $id,
                'for' => 'group',
            ]
            ,[
            'user_id' => auth()->id(),
            'for' => 'group',
            'for_id' => $id,
            'en_name' => $request->en_name,
            'zh_name' => $request->zh_name,
            'in_person' => $request->in_person,
            'electronic' => $request->electronic,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $type,
            'city' => $request->city,
            'district' => $request->district,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
        ]);
        $a = $this->certificate_show($id);
        return api_response($a->original, 'Certificate created');



    }
    public function certificate_show($id)
    {
        $certificate = Certificate::where('for' , 'group')->where('for_id', $id)->first();
        $a  = GroupClassReservation::findOrFail($id);
        $class = $a->groupClass;
        $return = [
            'id' => $certificate->id ,
            'en_name' => $certificate->en_name ,
            'zh_name' => $certificate->zh_name ,
            'email' => $certificate->email ,
            'phone' => $certificate->phone ,
            'electronic' => $certificate->electronic ,
            'type' => $certificate->type ,
            'city' => $certificate->city ,
            'district' => $certificate->district ,
            'address' => $certificate->address ,
            'postal_code' => $certificate->postal_code,
            'professor' =>  $class->professor->name ,
            'professor_id' =>  $class->professor->id ,
            'profile' =>  $class->professor->user->profile ,
            'session' =>  $class->schedules->count() ,
            'ageGroup' =>  $class->ageGroup->title ,
            'languageLevel' =>  $class->level->title ,
            'platform' =>  $class->platform->title ,
            'subgoal' =>  $class->subject->title ,
            'e_price' => Price::e_price,
            'p_price' => Price::p_price,
            'where' => 'One on One class Certification'

        ];
        return api_response($return);


    }
    public function submit(Request $request,$id)
    {
        $user = auth()->user();
        $balance = $user->wallet->balance;
        $certificate = Certificate::findOrFail($id);
        $type = $certificate->type;
        if ($type == 'electronic') {
            if ($balance < Price::e_price) {
                return api_response([], 'Insufficient price' ,422);
            }
            $certificate->update([
                'status' => 'approved'
            ]);
            return api_response($certificate, 'Certificate submitted');
        }
        if ($type == 'printable') {
            if ($balance < Price::p_price) {
                return api_response([], 'Insufficient price',422);
            }
            $certificate->update([
                'status' => 'approved'
            ]);
            return api_response($certificate, 'Certificate submitted');
        }
        return api_response([], 'Insufficient price',422);


    }


}
