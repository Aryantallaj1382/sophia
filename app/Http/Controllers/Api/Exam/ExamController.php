<?php

namespace App\Http\Controllers\Api\Exam;

use App\Helpers\PlanHelper;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAnswerOption;
use App\Models\ExamPart;
use App\Models\ExamStudent;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function final()
    {
        $exam = Exam::where('type', 'final')->paginate();
        $exam->getCollection()->transform(function ($exam) {
            return [
                'id' => $exam->id,
                'name' => $exam->name,
                'image' => $exam?->book->image ?? null,

            ];
        });
        return api_response($exam);
    }
    public function exams()
    {
        $exam = Exam::where('type', 'mock')->paginate();
        $exam->getCollection()->transform(function ($exam) {
            return [
                'id' => $exam->id,
                'name' => $exam->name,

            ];
        });
        return api_response($exam);
    }


    public function exam_show($id)
    {
        $exam = Exam::find($id);
        $exam->increment('view');
        // تعداد بخش‌ها
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

        $user = auth()->user() ?? null;
        $user_plan = null;
        if (auth()->user()) {
            $user_plan = UserPlan::where('user_id', auth()->id())
                ->where('is_active', 1)
                ->where(function ($query) {
                    $query->whereRelation('plan', 'plan_type', 'mock_test')
                        ->orWhereRelation('plan', 'plan_type', 'exam');
                })
                ->where('class_count', '>', 0)
                ->where('expires_at', '>=', Carbon::now())
                ->first();
        }


        $days_left = null;
        $hours_left = null;
        if ($user_plan) {
            $now = Carbon::now();
            $end = Carbon::parse($user_plan->expires_at);
            $days_left = $now->diffInDays($end);
            $hours_left = $now->diffInHours($end);

        }

        $return = [
            'id' => $exam->id,
            'name' => $exam->name,
            'expiration' => $exam->expiration,
            'number_of_attempts' => $exam->number_of_sections,
            'duration' => $exam->duration?->format("H:i:s") ?? null,
            'view' => $exam->view,
            'type' => $exam->type,
            'age_group' => $exam?->ageGroup?->title,
            'language_level' => $exam?->languageLevel?->title,
            'skill' => $exam?->skill?->name,
            'is_like' => $exam->is_like,
            'avg_rate' => (int)$exam->ratings()->avg('rating') ?? 0,
            'all_rate' => $exam->ratings()->count(),
            'rate' => $exam->rate,
            'listening' => $listening,
            'speaking' => $speaking,
            'writing' => $writing,
            'reading' => $reading,
            'reading_q' => $reading_q,
            'listening_q' => $listening_q,
            'speaking_q' => $speaking_q,
            'writing_q' => $writing_q,
            'has_plan' => $user_plan ? true : false,
            'days_left' => (int)$days_left ?? null,
            'hours_left' => (int)$hours_left ?? null,
            'color' => $user_plan?->plan?->color ?? null,
            'plan_name' => $user_plan?->plan->name ?? null,
            'class_count' => $user_plan?->plan->class_count ?? null,
            'class_left' => $user_plan?->class_count ?? 0,
            'part' => $part,


        ];
        return api_response($return);

    }

    public function toggleLike(Request $request, $id)
    {
        $group = Exam::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای لایک کردن لاگین کنید', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $group->id)
            ->where('likeable_type', Exam::class)
            ->first();

        if ($like) {
            $like->delete();
            return api_response(['is_like' => false], 'لایک حذف شد');
        }

        $group->likes()->create([
            'user_id' => $user->id,
        ]);

        return api_response(['is_like' => true], 'لایک شد');
    }

    public function comment(Request $request, $id)
    {

        $request->validate([
            'body' => 'nullable|string|min:1|max:400', // حداقل طول 1 برای جلوگیری از رشته خالی
            'video_url' => 'nullable|file',
            'voice_url' => 'nullable|file',
        ], [
            'body.min' => 'توضیحات نباید خالی باشد.',
            'body.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
            'video_url.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
            'voice_url.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
        ], [
            'body' => 'توضیحات',
            'video_url' => 'ویدیو',
            'voice_url' => 'ویس',
        ]);


        $groupClass = Exam::findOrFail($id);

        $videoUrl = null;
        $voiceUrl = null;

        // ذخیره فایل ویدیو
        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('comment/videos', 'public');
            $videoUrl = $videoPath;
        }

        // ذخیره فایل صدا
        if ($request->hasFile('voice_url')) {
            $voicePath = $request->file('voice_url')->store('comment/voices', 'public');
            $voiceUrl = $voicePath;
        }

        // ساخت نظر
        $comment = $groupClass->comments()->create([
            'user_id' => auth()->user()->id,
            'body' => $request->body,
            'video_url' => $videoUrl,
            'voice_url' => $voiceUrl,
        ]);

        return api_response([
            'id' => $comment->id,
            'body' => $comment->body,
            'video_url' => $videoUrl ? asset($videoUrl) : null,
            'voice_url' => $voiceUrl ? asset($voiceUrl) : null,
            'created_at' => $comment->created_at->diffForHumans(),
        ], 'نظر با موفقیت ثبت شد');
    }

    public function showComments(Request $request, $id)
    {
        $user_id = auth()->id();

        $groupClass = Exam::findOrFail($id);


        $query = $groupClass->comments()->with([
            'user.student',
        ])->where('admin_status', 'approved');

        if ($user_id) {
            $query->with([
                'likes' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                },
                'dislikes' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                },
            ]);
        }

        if ($request->has('type')) {
            $type = $request->input('type');

            if ($type == 'text') {
                $query->whereNull('video_url')->whereNull('voice_url');
            } elseif ($type == 'video') {
                $query->whereNotNull('video_url')->whereNull('body');
            } elseif ($type == 'voice') {
                $query->whereNotNull('voice_url')->whereNull('body');
            }
        }

        $comments = $query->latest()->paginate();

        $comments->getCollection()->transform(function ($comment) use ($user_id) {
            return [
                'id' => $comment->id,
                'content' => $comment->body ?? null,
                'video_url' => $comment->video_url ? $comment->video_url : null,
                'voice_url' => $comment->voice_url ? $comment->voice_url : null,
                'created_at' => $comment->created_at->toDateTimeString(),
                'user' => [
                    'user_name' => optional($comment->user)->name ?? null,
                    'image' => $comment->user->profile
                ],
                'likes_count' => $comment->likes->count(),
                'dislikes_count' => $comment->dislikes->count(),
                'is_like' => $user_id ? $comment->likes->isNotEmpty() : false,
                'is_dislike' => $user_id ? $comment->dislikes->isNotEmpty() : false,
            ];
        });

        return api_response($comments, 'ok');
    }

    public function rate(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای امتیازدهی باید لاگین کنید', 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $classGroup = Exam::findOrFail($id);

        $existingRating = $user->ratings()
            ->where('ratable_id', $classGroup->id)
            ->where('ratable_type', Exam::class)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            $existingRating->rating = $request->input('rating');
            $existingRating->save();
            return api_response([], '');
        }

        $classGroup->ratings()->create([
            'user_id' => $user->id,
            'rating' => $request->input('rating'),
        ]);

        return api_response([], '');
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function buy_exam(Request $request, $id)
    {
        $user = auth()->user();
        $exam = Exam::find($id);
        $exam_student = ExamStudent::where('exam_id', $id)->where('student_id', $user->id)->latest()->first();
        if (!$exam_student || $exam_student->status == 'completed') {
            $result = PlanHelper::reserveClass($user->id, 'mock_test', $request, $id);
            if (!$result['success']) {
                return response()->json(['message' => $result['message']], 422);
            }
            return api_response([], 'exam bought');

        }
        return api_response([], 'you already buy this exam');
    }

    public function part_0($examId)
    {
        $exam = Exam::find($examId);

        if (!$exam) {
            return api_response([], 'چیزی یافت نشد');
        }

        return [
            'id' => $exam->id,
            'part_id' => 0,
            'part_title' => $exam->name,
            'description' => $exam->description,
            'duration' => $exam?->duration?->format('H:i:s') ?? null,
            'total_part' => $exam->parts->count(),
        ];
    }

    public function question(Request $request, $examId)
    {
        $student = auth()->user();

        $exam = Exam::find($examId);
        if (!$exam) {
            return api_response([], 'چیزی یافت نشد');
        }

        $partNumber = $request->input('part', 0);


        $exam_student = ExamStudent::where('exam_id', $examId)->where('student_id', $student->id)->where('status', '!=', 'completed')->where('student_id', $student->id)->latest()->first();
        if (!$exam_student) {
            return api_response([], 'please puy the exam', 422);
        } elseif ($exam_student->status == 'completed') {
            return api_response([], 'expired exam time', 422);

        } elseif ($exam_student->expired_at < Carbon::now() && $exam_student->expired_at !== null) {
            return api_response([], 'expired exam time', 422);
            $exam_student->update([
                'status' => 'completed',
            ]);
        }
        $hours = (int)$exam->duration->format('H'); // ساعت
        $minutes = (int)$exam->duration->format('i'); // دقیقه
        $updateData = ['status' => 'in_progress', 'started_at' => Carbon::now()];
        if (is_null($exam_student->expired_at)) {
            $updateData['expired_at'] = Carbon::now()->addHours($hours)->addMinutes($minutes);
        }



        $exam_student->update($updateData);


        if ($partNumber == 0) {
            $data = $this->part_0($examId , $exam_student->expired_at);
            return response()->json($data);
        }
        $part = $exam->parts()->where('number', $partNumber)->first();
        if (!$part) {
            return api_response([], 'بخش مورد نظر یافت نشد');
        }

        $questions = $part->questions()->with('media', 'variants.options', 'types')->get();

        $mappedQuestions = $questions->map(function ($question) {
            $base = [
                'id' => $question->id,
                'title' => $question->title ?? '',
                'description' => $question->description ?? '',
                'questionType' => $question->question_type,
                'medias' => $question->media->map(function ($media) {
                    return [
                        'media_path' => $media->path,
                        'description' => $media->description ?? '',
                    ];
                }),
            ];

            if ($question->question_type == 'test') {
                $base['variants'] = $question->variants->map(function ($variant) {

                    $correctCount = $variant->options->where('is_correct', true)->count();

                    return [
                        'id' => $variant->id,
                        'question' => $variant->text ?? '',
                        'multiple_correct' => $correctCount > 1,

                        'options' => $variant->options->map(function ($opt) {
                            return [
                                'id' => $opt->id,
                                'text' => $opt->text,

                            ];
                        })->values(),
                    ];
                });
            } else {
                $base['question'] = $question->question;
            }

            return $base;
        });
        $now = Carbon::now('UTC')->floorSecond(); // نادیده گرفتن میلی‌ثانیه
        $expired = Carbon::parse($exam_student->expired_at)->setTimezone('UTC')->floorSecond();

        if ($expired->greaterThan($now)) {
            $diffInSeconds = $expired->diffInSeconds($now);

            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;

            $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            $duration = '00:00:00';
        }
        $api = [

            'part_id' => $part->id,
            'part_title' => $part->title,
            'duration' => '18:18:18' ,

            'passenger' => $part->passenger,
            'passenger_title' => $part->passenger_title,
            'questions_title' => $part->question_title,
            'partType' => $part->type->name,
            'medias' => $part->media->map(function ($media) {
                $ext = Str::lower(pathinfo($media->path, PATHINFO_EXTENSION));

                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a'];

                if (in_array($ext, $imageExtensions)) {
                    $type = 'image';
                } elseif (in_array($ext, $audioExtensions)) {
                    $type = 'audio';
                } else {
                    $type = 'other';
                }
                return [
                    'media_path' => $media->path,
                    'description' => $media->description ?? '',
                    'media_type' => $type,
                ];
            }),
            'description' => $part->text,
            'questions' => $mappedQuestions,
        ];

        return response()->json($api);
    }

    public function submitAnswers(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:exam_questions,id',
            'answers.*.text_answer' => 'nullable|string',
            'answers.*.file' => 'nullable', // می‌تونه نیاد یا null باشه
            'answers.*.variant_id' => 'nullable',
            'answers.*.option_ids' => 'nullable|array',
            'answers.*.option_ids.*' => 'exists:exam_variant_options,id',
        ]);

        $studentId = auth()->id();

        foreach ($request->answers as $item) {
            $variantId = $item['variant_id'] ?? null;

            // جواب قبلی
            $examAnswer = ExamAnswer::where('exam_question_id', $item['question_id'])
                ->where('student_id', $studentId)
                ->first();

            $filePath = $examAnswer->file ?? null;

            // فقط اگر کلید file توی request هست بررسی کنیم
            if (array_key_exists('file', $item)) {
                if ($item['file'] instanceof \Illuminate\Http\UploadedFile) {
                    // آپلود جدید
                    $fileName = time() . '_' . uniqid() . '.' . $item['file']->getClientOriginalExtension();
                    $destinationPath = public_path('exam_answers');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $item['file']->move($destinationPath, $fileName);
                    $filePath = 'exam_answers/' . $fileName;

                } elseif ($item['file'] === null) {
                    // حذف فایل
                    $filePath = null;

                } elseif (is_string($item['file']) && $item['file'] === $filePath) {
                    // همون لینک قبلی → تغییر نده
                    $filePath = $examAnswer->file ?? null;
                }
            }

            // آپدیت یا ساخت جواب
            if ($examAnswer) {
                $examAnswer->update([
                    'exam_variant_id' => $variantId,
                    'text_answer'     => $item['text_answer'] ?? null,
                    'file'            => $filePath,
                ]);
            } else {
                $examAnswer = ExamAnswer::create([
                    'exam_question_id' => $item['question_id'],
                    'student_id'       => $studentId,
                    'exam_variant_id'  => $variantId,
                    'text_answer'      => $item['text_answer'] ?? null,
                    'file'             => $filePath,
                ]);
            }

            // مدیریت گزینه‌ها
            ExamAnswerOption::where('exam_answer_id', $examAnswer->id)->delete();
            if (!empty($item['option_ids'])) {
                foreach (array_unique($item['option_ids']) as $optionId) {
                    ExamAnswerOption::create([
                        'exam_answer_id'         => $examAnswer->id,
                        'exam_variant_id'        => $variantId,
                        'exam_variant_option_id' => $optionId,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'All answers submitted successfully'
        ]);
    }

    public function beforeFinishExam(Request $request, $id)
    {
        $exam = Exam::with('parts.questions')->findOrFail($id);
        $totalQuestions = $exam->parts->sum(fn($part) => $part->questions->count());

        $answeredCount = ExamAnswer::where('student_id', auth()->id())
            ->whereHas('question.part.exam', function ($q) use ($id) {
                $q->where('exams.id', $id);
            })
            ->count();

        $no_answer = $totalQuestions - $answeredCount;

        return api_response([
            'total_questions' => $totalQuestions,
            'answered_count' => $answeredCount,
            'no_answer' => $no_answer,
            'name' => auth()->user()->name,

        ]);
    }

    public function finishExam(Request $request, $id)
    {
        $user = auth()->user();
        $exam_student = ExamStudent::where('student_id', $user->id)->where('exam_id', $id)->first();
        $exam_student->update([
            'status' => 'completed',
            'finished_at' => now(),
        ]);
        return api_response([], 'exam finished successfully and you can see result in your panel soon ');

    }

    public function getPartAnswers(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'part_id' => 'required|integer'
        ]);

        $studentId = auth()->user()->id;
        $part = ExamPart::where('exam_id', $request->exam_id)
            ->where('number', $request->part_id)
            ->firstOrFail();
        $answers = ExamAnswer::with(['options'])
            ->where('student_id', $studentId)
            ->whereIn('exam_question_id', $part->questions()->pluck('id'))
            ->get();

        $result = $answers->map(function ($answer) {
            return [
                'question_id' => (int)$answer->exam_question_id,
                'text_answer' => $answer->text_answer,
                'types' => $answer->question->question_type,
                'file' => $answer->file,
                'variant_id' =>  (int)$answer?->options()?->first()?->exam_variant_id ?? null,
                'option_ids' => $answer->options->pluck('exam_variant_option_id')->toArray(),
            ];
        });

        return response()->json($result);
    }


}
