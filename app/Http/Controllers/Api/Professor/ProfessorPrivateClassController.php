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
        $private = PrivateClassReservation::with('user')->where('professor_id', $user->id)->paginate(12);
        $private->getCollection()->transform(function ($item, $key) {

            $pastSessionsCount = $item->timeSlots
                ->where('date', '<', now()->toDateString())
                ->count();
            $currentSessionNumber = min($pastSessionsCount + 1, $item->sessions_count);

            return [
                'id' => $item->id,
                'professor_id' => $item->user(),
                'professor_name' => $item->user?->student?->nickname,
                'professor_profile' => $item->user?->profile,
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
            'professor_name' => $private->user->name,
            'professor_id' => $private->user->id,
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
            'reserve_time' => $private->created_at?->format('D ,j M Y , H:i'),

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
                $is_report = ReportRegistration::where('private_professor_time_slot', $item->id)->exists();
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
                    'is_report' => $is_report,
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
            'cancel_by' => 'professor',
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
            'absence_time' => 'nullable',
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
            'report_home_work.*.title' => 'nullable|string',
            'report_home_work.*.is_reading' => 'nullable',
        ]);

        $report = ReportRegistration::updateOrCreate([
            'private_professor_time_slot' => $request->private_professor_time_slot,
        ],[
            'absence' => $validated['absence'],
            'absence_time' => $validated['absence_time'] ??null,
            'writing' => $validated['writing'] ?? null,
            'speaking' => $validated['speaking'] ?? null,
            'class_id' => $validated['class_id'],
            'reading' => $validated['reading'] ?? null,
            'listening' => $validated['listening'] ?? null,
            'vocabulary' => $validated['vocabulary'] ?? null,
            'grammar' => $validated['grammar'] ?? null,
            'student_status' => $validated['student_status'] ?? null,
            'strengths' => $validated['strengths'] ?? null,
            'weaknesses' => $validated['weaknesses'] ?? null,
            'solutions' => $validated['solutions'] ?? null,
            'private_professor_time_slot' => $validated['private_professor_time_slot'],
        ]);

        if (!empty($validated['report_home_work'])) {
            // حذف همه رکوردهای قدیمی مرتبط با این گزارش
            ReportHomeWork::where('report_registration_id', $report->id)->delete();

            // ذخیره رکوردهای جدید
            foreach ($validated['report_home_work'] as $homework) {
                ReportHomeWork::create([
                    'title' => $homework['title'],
                    'is_reading' => $homework['is_reading'] ?? null,
                    'report_registration_id' => $report->id,
                ]);
            }
        }

        if ($validated['absence'] == 'absence') {
            $slot = PrivateProfessorTimeSlot::find($validated['private_professor_time_slot']);
            $slot->update([
                'status' => 'Absent'
            ]);
        }
        if ($validated['absence'] == 'presence') {
            $slot = PrivateProfessorTimeSlot::find($validated['private_professor_time_slot']);
            $slot->update([
                'status' => 'Finished'
            ]);
        }
        if ($validated['absence'] == 'delay') {
            $slot = PrivateProfessorTimeSlot::find($validated['private_professor_time_slot']);
            $slot->update([
                'status' => 'Finished'
            ]);
        }


        return api_response([],'success');
    }
    public function report_show($id)
    {
        $report = ReportRegistration::where('private_professor_time_slot' , $id)->first();

        $reports = [

            'absence' => $report->absence ?? null,
            'absence_time' => $report->absence_time ??null,
            'writing' => $report->writing ?? null,
            'speaking' => $report->speaking ?? null,
            'class_id' => $report->class_id ??null,
            'reading' => $report->reading ?? null,
            'listening' => $report->listening ?? null,
            'vocabulary' => $report->vocabulary ?? null,
            'grammar' => $report->grammar ?? null,
            'strengths' => $report->strengths ??  [],
            'weaknesses' => $report->weaknesses ??  [],
            'solutions' => $report->solutions ??  [],
            'report_home_work' => $report->homeWorks ?? [],
            'private_professor_time_slot' => $report->private_professor_time_slot ?? null,
        ];
        return api_response($reports);

    }


}
