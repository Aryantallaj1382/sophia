<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\contact\Price;
use App\Models\PrivateClassReservation;
use App\Models\PrivateProfessorTimeSlot;
use App\Models\ReportHomeWork;
use App\Models\ReportRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrivateClassController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $private = PrivateClassReservation::where('user_id', $user->id)->paginate(12);
        $private->getCollection()->transform(function ($item, $key) {
            $pastSessionsCount = $item->timeSlots
                ->where('date', '<', now()->toDateString())
                ->count();
            $currentSessionNumber = min($pastSessionsCount + 1, $item->sessions_count);

            return [
                'id' => $item->id,
                'professor_id' => $item->professor_id,
                'professor_name' => $item->professor->name,
                'professor_profile' => $item->professor->user->profile,
                'subgoal' => $item->subgoal->goal->title . ' (' . $item->subgoal->title . ')',
                'date' => $item->timeSlots()
                    ->where('date', '>=', now()->toDateString())
                    ->orderBy('date', 'asc')
                    ->first()?->date?->format('Y-m-d'),
                'time' => $item->timeSlots()
                    ->where('date', '>=', now()->toDateString())
                    ->orderBy('date', 'asc')
                    ->first()?->time?->format('H:i'),
                'count' => $item->timeSlots->count(),
                'session_number' => $currentSessionNumber, // اضافه شد
            ];
        });

        return api_response($private);
    }

    public function new_class($id)
    {
        $class = PrivateClassReservation::find($id);

        $futureSessions = PrivateProfessorTimeSlot::where('private_class_reservation_id', $id)
            ->whereDate('date', '>', now())->where('status' , 'upcoming')->orWhere('status' , 'today')
            ->get();
        $session = $futureSessions->first();
        $session_count = PrivateProfessorTimeSlot::where('private_class_reservation_id', $id)->count();
        $certificate = Certificate::where('for' , 'private')->where('for_id', $id)->exists();
        $certificate_file = Certificate::where('for' , 'private')->where('for_id', $id)->first();
        $a = [
            'link' => $class->class_link ?? null,
            'professor_id' => $class->professor->user->id ?? null,
            'profile' => $class->professor->user->profile ?? null,
            'subgoal' => $class->subgoal->goal->title . ' (' . $class->subgoal->title . ')',
            'class' => $session ? $session->session_number . ' from ' . $session_count : null,
            'session_id' => $session ? $session->id : null,
            'date' => $session ? $session->date->format('j F') : null,
            'time' => $session ? $session->time->format('H:i') : null,
            'is_finished' => $futureSessions->isEmpty(),
            'has_certificate' => $certificate,
            'certificate_file' => $certificate_file->file ?? null,
        ];

        return api_response($a);
    }


    public function information($id)
    {
        $private = PrivateClassReservation::find($id);
        $book = $private?->reservedBooks?->first();
        $return = [
            'id' => $private->id,
            'professor_name' => $private->professor->name,
            'professor_id' => $private->professor->id,
            'count' => $private->timeSlots->count(),
            'ageGroup' => $private->ageGroup->title,
            'languageLevel' => $private->languageLevel->title,
            'platform' => $private->platform->title,
            'description' => $private->description,
            'subgoal' => $private->subgoal->goal->title . ' (' . $private->subgoal->title . ')',
            'book' => [
                'name' => $book?->book?->name,
                'selection_type' => $book?->selection_type,
                'username' => $book?->username,
                'image' => $book?->book?->image,
                'password' => $book?->password,
                'link' => $book?->link,
                'file' => $book?->file,
            ],
            'reserve_time' => $private->created_at->format('D ,j M Y , H:i'),

        ];
        return api_response($return);
    }

    public function sessions(Request $request, $id)
    {
        $upcoming = $request->boolean('upcoming', false); // مقدار پیش‌فرض false
        $private = PrivateClassReservation::find($id);

        if (!$private) {
            return []; // اگر پیدا نشد، آرایه خالی
        }
        $link = $private->class_link;
        $professor_id = $private->professor->user->id;

        $timeSlots = $private->timeSlots;

        if ($upcoming) {
            $timeSlots = $timeSlots->filter(function ($slot) {
                return $slot->status === 'Upcoming' || $slot->status === 'Today';
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
                    'time' => $item->time->format('H:i'),
                    'origin_date' => $item->date->copy()->setTimeFrom($item->time), // تاریخ و ساعت با هم
                    'status' => $item->status,
                    'class_link' => $link,
                    'professor_id' => $professor_id,
                    'cancel_by' => $item->cancel_by,
                    'cancel_date' => $item->cancel_date,
                    'cancel_reason' => $item->cancel_reason,
                    'cancel_reason_file' => $item->cancel_reason_file,
                    'refund' => $item->refund,
                ];
            })
            ->toArray();
    }


    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required',
            'cancel_reason_file' => 'nullable',
        ]);

        $user = auth()->user();
        $private = PrivateProfessorTimeSlot::whereRelation('reservation', 'user_id', $user->id)->find($id);
        if (!$private) {
            return api_response(null, 'Reservation not found', 404);
        }
        if ($request->file) {
            $file = $request->file('cancel_reason_file')->store('cancel_reason_file', 'public');
        }
        $private->update([
            'cancel_reason' => $request->cancel_reason,
            'cancel_reason_file' => null,
            'status' => 'cancelled',
        ]);
        return api_response([], 'cancel');
    }


    public function calender($id)
    {
        $user = auth()->user();
        if (!$user) {
            return api_response(null, 'Unauthorized', 401);
        }
        $private = PrivateClassReservation::find($id);
        if (!$private) {
            return api_response(null, 'Reservation not found', 404);
        }
        $timeSlots = $private->timeSlots()->get();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $calendar = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $daySessions = $timeSlots->filter(function ($slot) use ($date) {
                return $slot->date->isSameDay($date);
            })->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'origin_date' => $slot->date->copy()->setTimeFrom($slot->time)->toDateTimeString(),
                    'date' => $slot->date->format('l'),
                    'SessionNumber' => $slot->session_number,
                    'status' => $slot->status,
                    'class_link' => $slot->reservation->class_link,
                    'time' => $slot->time->format('H:i'),
                ];
            })->values();
            $calendar[] = [
                'day' => $date->format('Y-m-d'),
                'sessions' => $daySessions,
            ];
        }

        return api_response(['data' => $calendar]);
    }


    public function home_work($id)
    {
        $user = auth()->user();

        $class = PrivateClassReservation::where('user_id', $user->id)->findOrFail($id);

        $sessions = $class->timeSlots()
            ->with('registrations.homeWorks')
            ->get()
            ->map(function ($item) {
                $firstHomeWork = $item->registrations->first()?->homeWorks->first();

                return [
                    'session_number' => $item->session_number,
                    'home_work' => $firstHomeWork ? [
                        'id' => $firstHomeWork->id,
                        'title' => $firstHomeWork->title,
                        'answer' => $firstHomeWork->answer,
                        'is_reading' => (int)$firstHomeWork->is_reading,
                        'status' => $firstHomeWork->status,
                    ] : null,
                ];
            });


        return api_response($sessions);
    }

    public function upload_answer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'nullable|file|max:10240', // حداکثر 10 مگابایت
            'doing' => 'nullable|in:yes,no',     // فقط yes یا no مجاز
        ]);

        $user = auth()->user();
        $home = ReportHomeWork::find($id);

        if (!$home) {
            return api_response(null, 'HomeWork not found', 404);
        }

        $data = [];

        if ($request->hasFile('answer')) {
            $data['answer'] = $request->file('answer')->store('answers', 'public');
        }

        if ($request->filled('doing')) {
            $data['status'] = $request->doing;
        }

        if (!empty($data)) {
            $home->update($data);
        }

        return api_response([]);
    }


//    public function activityReport($id)
//    {
//        $class = PrivateClassReservation::with('timeSlots.registrations')->findOrFail($id);
//
//        $report = $class->timeSlots
//            ->sortBy('session_number')
//            ->values()
//            ->map(function ($slot) {
//                $registration = $slot->registrations->first(); // فرض بر اینکه هر جلسه فقط یک ثبت‌نام است
//                return [
//                    'session_number' => $slot->session_number,
//                    'Writing'       => $registration?->writing ?? null,
//                    'Speaking'      => $registration?->speaking ?? null,
//                    'Reading'       => $registration?->reading ?? null,
//                    'Vocabulary'   => $registration?->vocabulary ?? null,
//                    'Comprehension'=> $registration?->comprehension ?? null,
//                    'Accent'       => $registration?->accent ?? null,
//                ];
//            });
//
//        return api_response($report);
//    }
    public function activityReport($id)
    {
        $class = PrivateClassReservation::with('timeSlots.registrations')->findOrFail($id);

        $report = [
            'Writing' => [],
            'Speaking' => [],
            'Reading' => [],
            'Vocabulary' => [],
            'Grammar' => [],
            'Listening' => [],
        ];

        foreach ($class->timeSlots->sortBy('session_number') as $slot) {
            $registration = $slot->registrations->first();

            $report['Writing'][] = (int)($registration->writing ?? 0);
            $report['Speaking'][] = (int)($registration->speaking ?? 0);
            $report['Reading'][] = (int)($registration->reading ?? 0);
            $report['Vocabulary'][] = (int)($registration->vocabulary ?? 0);
            $report['Grammar'][] = (int)($registration->grammar ?? 0);
            $report['Listening'][] = (int)($registration->listening ?? 0);
        }

        return api_response(['data' => $report]);
    }


    public function report_table($id)
    {
        $user = auth()->user();
        $class = PrivateClassReservation::with('timeSlots.registrations')->findOrFail($id);

        $report = $class->timeSlots
            ->map(function ($item) {
                $firstRegistration = $item->registrations->first(); // اولین ثبت‌نام هر جلسه

                return [
                    'session_number' => $item->session_number,
                    'id' => $firstRegistration?->id,
                    'date' => $firstRegistration?->created_at?->format('d M Y'),
                    'time' => $firstRegistration?->created_at?->format('H:i'),
                    'origin_date' => $firstRegistration?->created_at?->toDateTimeString(),
                    'score' => $firstRegistration?->final_score,
                ];
            })
            ->filter(fn($row) => !is_null($row['score'])) // فقط جلساتی که گزارش دارن
            ->values(); // ایندکس‌گذاری مجدد

        return api_response($report);
    }

    public function delay($id)
    {
        $user = auth()->user();
        $class = PrivateClassReservation::with('timeSlots.registrations')->findOrFail($id);

        $report = $class->timeSlots->map(function ($item) {
            // فقط اولین ثبت‌نام با تأخیر
            $firstRegistration = $item->registrations->firstWhere('absence', 'delay');

            if (!$firstRegistration) {
                return null; // جلسه‌ای بدون ثبت‌نام تأخیری را بعدا حذف می‌کنیم
            }

            return [
                'session_number' => $item->session_number,
                'id' => $firstRegistration->id,
                'date' => $firstRegistration->created_at->format('d M Y'),
                'time' => $firstRegistration->created_at->format('H:i'),
                'origin_date' => $firstRegistration->created_at->toDateTimeString(),
                'absence_time' => $firstRegistration->absence_time,
            ];
        })
            ->filter()
            ->values();
        $allRegistrations = $class->timeSlots->flatMap->registrations;

        $absent = $allRegistrations->where('absence', 'absence')->count();
        $no_absent = $allRegistrations->where('absence', '!=', 'absence')->count();
        return api_response([
            'data' => $report,
            'absent' => $absent,
            'no_absent' => $no_absent,
        ]);
    }

    public function activity($id)
    {
        $class = ReportRegistration::find($id);
        $return = [
            'strengths' => $class->strengths,
            'weaknesses' => $class->weaknesses,
            'solutions' => $class->solutions,
            'writing' => $class->writing ?? 0,
            'speaking' => $class->speaking?? 0,
            'reading' => $class->reading?? 0,
            'vocabulary' => $class->vocabulary?? 0,
            'listening' => $class->listening?? 0,
            'grammar' => $class->grammar?? 0,

        ];
        return api_response($return);
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
        $certificate = Certificate::where('for' , 'private')->where('for_id', $id)->exists();
//        if ($certificate) {
//            return api_response([], 'Certificate already exists');
//        }
        Certificate::create([
            'user_id' => auth()->id(),
            'for' => 'private',
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
        $certificate = Certificate::where('for' , 'private')->where('for_id', $id)->first();
        $class  = PrivateClassReservation::with('timeSlots.registrations')->findOrFail($id);
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
            'session' =>  $class->timeSlots->count() ,
            'ageGroup' =>  $class->ageGroup->title ,
            'languageLevel' =>  $class->languageLevel->title ,
            'platform' =>  $class->platform->title ,
            'subgoal' =>  $class->subgoal->title ,
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
