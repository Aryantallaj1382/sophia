<?php

namespace App\Http\Controllers\Api\Professor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\PrivateClassReservation;
use App\Models\PrivateProfessorTimeSlot;
use App\Models\ReportHomeWork;
use App\Models\ReportRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfessorPrivateClassController extends Controller
{
    public function index()
    {
        $user =  auth()->user()->professor;
        $private = PrivateClassReservation::where('professor_id', $user->id)->paginate(12);
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
                'session_number' => $currentSessionNumber,
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
            'student_id' => $class->user->id ?? null,
            'profile' => $class->user->profile ?? null,
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
            return [];
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

        $user = auth()->user()->professor;
        $private = PrivateProfessorTimeSlot::whereRelation('reservation', 'professor_id', $user->id)->find($id);
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
    public function report(Request $request)

    {
        $validated = $request->validate([
            'private_professor_time_slot' => 'required',
            'absence' => 'required|in:absence,presence,delay',
            'absence_time' => 'nullable|string',
            'exam' => 'nullable|boolean',
            'speaking' => 'nullable|integer',
            'writing' => 'nullable|integer',
            'speaking_score' => 'nullable|integer',
            'reading' => 'nullable|integer',
            'listening' => 'nullable|integer',
            'vocabulary' => 'nullable|integer',
            'final_score' => 'nullable|integer',
            'grammar' => 'nullable|integer',
            'student_status' => 'nullable|in:passed,rejected',
            'exam_solutions' => 'nullable|array',
            'strengths' => 'nullable|array',
            'weaknesses' => 'nullable|array',
            'solutions' => 'nullable|array',
            'score' => 'nullable|array',
            'skills' => 'nullable|array',
            'exam_part' => 'nullable|array',
            'class_id' => 'required',
            'report_home_work' => 'nullable|array',
            'report_home_work.*.title' => 'required|string',
            'report_home_work.*.answer' => 'required|string',
            'report_home_work.*.is_reading' => 'nullable',
            'report_home_work.*.status' => 'nullable|in:yes,no',
        ]);

        $report = ReportRegistration::create([
            'absence' => $validated['absence'],
            'absence_time' => $validated['absence_time'] ??null,
            'exam' => $validated['exam'] ?? false,
            'writing' => $validated['writing'] ?? null,
            'speaking' => $validated['speaking_score'] ?? null,
            'class_id' => $validated['class_id'],
            'reading' => $validated['reading'] ?? null,
            'listening' => $validated['listening'] ?? null,
            'vocabulary' => $validated['vocabulary'] ?? null,
            'final_score' => $validated['final_score'] ?? null,
            'grammar' => $validated['grammar'] ?? null,
            'student_status' => $validated['student_status'] ?? null,
            'exam_solutions' => $validated['exam_solutions'] ?? null,
            'strengths' => $validated['strengths'] ?? null,
            'weaknesses' => $validated['weaknesses'] ?? null,
            'solutions' => $validated['solutions'] ?? null,
            'score' => $validated['score'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'exam_part' => $validated['exam_part'] ?? null,
            'private_professor_time_slot' => $validated['private_professor_time_slot'],
        ]);

        if (!empty($validated['report_home_work'])) {
            foreach ($validated['report_home_work'] as $homework) {
                ReportHomeWork::create([
                    'title' => $homework['title'],
                    'answer' => $homework['answer'],
                    'status' => $homework['status'] ?? null,
                    'is_reading' => $homework['is_reading'] ?? null,
                    'report_registration_id' => $report->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'گزارش با موفقیت ثبت شد.',
            'data' => $report
        ]);
    }


}
