<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\ExamStudent;
use App\Models\ExamStudentResult;
use App\Models\ProfessorPlacement;
use Illuminate\Http\Request;

class ExamStudentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');
        $model = $request->query('model');
        $user = auth()->user()->id;

        $exam  = ExamStudent::whereRelation('exam','type', $type)
            ->where('status' , $status)
            ->with('exam.languageLevel', 'exam.ageGroup', 'exam.book')
            ->get()
            ->map(function ($item) {
                $expirationMonths = (int) $item->exam->expiration;
                $expireAt = $item->created_at->copy()->addMonths($expirationMonths);
                $diff = now()->diff($expireAt);
                if ($diff->invert) {
                    $remainingText = 'expired';
                } elseif ($diff->m > 0) {
                    $remainingText = $diff->m . ' month and ' . $diff->d . ' day';
                } else {
                    $remainingText = $diff->d . ' day';
                }

                return [
                    'id'=> $item->id,
                    'name' => $item->exam->name,
                    'level'=> $item->exam?->languageLevel?->title ?? null,
                    'age'=> $item->exam?->ageGroup?->title ?? null,
                    'image' => $item->exam?->book?->image ?? null,
                    'expire_at'=> $expireAt->toDateString(),
                    'remaining_time'=> $remainingText,
                ];
            });

        if ($model == 'teacher') {
            $exam  = ProfessorPlacement::where('users_id', $user)->get()->map(function ($item) {
                return [
                    'id'=> $item->id,
                    'name' => $item->name,
                    'level'=> $item->languageLevel?->title ?? null,
                    'age'=> $item->ageGroup?->title ?? null,
                    'skill'=> $item->skill?->name ?? null,
                    'expire_at'=> $item->time,
                    'model'=> 'teacher',
                ];
            });



        }

        return api_response(['data'=>$exam]);
    }

    public function show($id)
    {
        $exam_s = ExamStudent::find($id);
        $exam= $exam_s->exam;
        $type = $exam_s->exam->type;
        $expirationMonths = (int) $exam_s->exam->expiration;
        $expireAt = $exam_s->created_at->copy()->addMonths($expirationMonths);
        $diff = now()->diff($expireAt);
        if ($diff->invert) {
            $remainingText = 'expired';
        } elseif ($diff->m > 0) {
            $remainingText = $diff->m . ' month and ' . $diff->d . ' day';
        } else {
            $remainingText = $diff->d . ' day';
        }

        $listening = $exam->parts()->whereRelation('type', 'name', 'listening')->count();
        $speaking = $exam->parts()->whereRelation('type', 'name', 'speaking')->count();
        $writing = $exam->parts()->whereRelation('type', 'name', 'writing')->count();
        $reading = $exam->parts()->whereRelation('type', 'name', 'reading')->count();
        $part = $exam->parts()->count();

// تعداد سوالات هر نوع بخش
        $listening_q = $exam->parts()
            ->whereRelation('type', 'name', 'listening')
            ->withCount('questions')
            ->get()
            ->sum('questions_count');

        $speaking_q = $exam->parts()
            ->whereRelation('type', 'name', 'speaking')
            ->withCount('questions')
            ->get()
            ->sum('questions_count');

        $writing_q = $exam->parts()
            ->whereRelation('type', 'name', 'writing')
            ->withCount('questions')
            ->get()
            ->sum('questions_count');

        $reading_q = $exam->parts()
            ->whereRelation('type', 'name', 'reading')
            ->withCount('questions')
            ->get()
            ->sum('questions_count');
        $part = $exam->parts()->count();


       if ($type == 'final')
       {
           $return = [
               'id' => $exam_s->id,
               'status' => $exam_s->status,
               'is_result' => $exam_s->results()->exists() ? 1 : 0,
               'name' => $exam_s->exam->name,
               'image' => $exam_s->exam->book->image,
               'type' => $exam_s->exam->type,
               'duration' => $exam->duration?->format("H:i:s") ?? null,
               'remaining_time'=> $remainingText,
               'date' => $exam_s->created_at->format('D, d M Y , H:i:s'),
               'listening' => $listening,
               'speaking' => $speaking,
               'writing' => $writing,
               'reading' => $reading,
               'reading_q' => $reading_q,
               'listening_q' => $listening_q,
               'speaking_q' => $speaking_q,
               'writing_q' => $writing_q,
               'part' => $part,
               'number_of_attempts' => $exam->number_of_sections,

           ];
       }
        if ($type == 'placement')
        {
            $return = [
                'id' => $exam_s->id,
                'status' => $exam_s->status,
                'is_result' => $exam_s->results()->exists() ? 1 : 0,

                'name' => $exam_s->exam->name,
                'level'=> $exam_s->exam?->languageLevel?->title ?? null,
                'age'=> $exam_s->exam?->ageGroup?->title ?? null,
                'type' => $exam_s->exam->type,
                'remaining_time'=> $remainingText,
                'date' => $exam_s->created_at->format('D, d M Y , H:i:s'),
                'listening' => $listening,
                'speaking' => $speaking,
                'writing' => $writing,
                'reading' => $reading,
                'reading_q' => $reading_q,
                'listening_q' => $listening_q,
                'speaking_q' => $speaking_q,
                'writing_q' => $writing_q,
                'part' => $part,
                'number_of_attempts' => $exam->number_of_sections,
                'duration' => $exam->duration?->format("H:i:s") ?? null,

            ];
        }
        if ($type == 'mock')
        {
            $return = [
                'id' => $exam_s->id,
                'status' => $exam_s->status,
                'is_result' => $exam_s->results()->exists() ? 1 : 0,
                'name' => $exam_s->exam->name,
                'type' => $exam_s->exam->type,
                'remaining_time'=> $remainingText,
                'date' => $exam_s->created_at->format('D, d M Y , H:i:s'),
                'listening' => $listening,
                'speaking' => $speaking,
                'writing' => $writing,
                'reading' => $reading,
                'reading_q' => $reading_q,
                'listening_q' => $listening_q,
                'speaking_q' => $speaking_q,
                'writing_q' => $writing_q,
                'part' => $part,
                'number_of_attempts' => $exam->number_of_sections,
                'duration' => $exam->duration?->format("H:i:s") ?? null,

            ];
        }


        return api_response($return ?? null);

    }




    public function result($id)
    {
        $exam = ExamStudentResult::where('exam_student_id', $id)->first();
        return api_response([
            'id' => $exam->id,
            'status' => $exam->status,
            'name' => $exam->id,
            'score' => $exam->score,
            'time' => $exam->date->format('D, d M Y , H:i:s'),
            'speaking' => $exam->speaking,
            'reading' => $exam->reading,
            'listening' => $exam->listening,
            'writing' => $exam->writing,
            'file' => $exam->file,
        ]);

    }

    public function show_placement($id)
    {
        $exam  = ProfessorPlacement::find($id);
        $return = [
            'id' => $exam->id,
            'status' => $exam->status,
            'is_result' => $exam->status ? 1 : 0,
            'name' => $exam->name,
            'level'=> $exam?->languageLevel?->title ?? null,
            'age'=> $exam?->ageGroup?->title ?? null,
            'date' => $exam->created_at->format('D, d M Y , H:i:s'),
            'number_of_attempts' => $exam->number_of_sections,
            'duration' => $exam->duration?->format("H:i:s") ?? null,

        ];
        return api_response($return ?? null);

    }
    public function result_placement($id)
    {
        $exam = ProfessorPlacement::find($id);
        return api_response([
            'id' => $exam->id,
            'status' => $exam->status,
            'name' => $exam->name,
            'score' => $exam->score,
            'time' => $exam->exam_date,
            'speaking' => $exam->speaking,
            'reading' => $exam->reading,
            'listening' => $exam->listening,
            'writing' => $exam->writing,
            'file' => $exam->file,
        ]);

    }
}
