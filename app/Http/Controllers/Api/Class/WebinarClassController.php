<?php

namespace App\Http\Controllers\Api\Class;

use App\Helpers\PlanHelper;
use App\Http\Controllers\Controller;
use App\Models\GroupClass;
use App\Models\GroupClassReservation;
use App\Models\UserPlan;
use App\Models\Webinar;
use App\Models\WebinarReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebinarClassController extends Controller
{
    public function index()
    {
        $class = Webinar::paginate();
        $class->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'image' => $item->image,
                'time' => $item->time?->format('H:i'),
                'date' => $item->date?->format('m/d'),
                'name' => $item->subject->title,
                'professor' => $item->professor->name,
                'profile' => $item->professor->user->profile
            ];
        });
        return api_response($class);
    }
    public function show($id)
    {
        $class = Webinar::find($id);
        $return = [
            'id' => $class->id,
            'name' => $class->name,
            'topic'=> $class->subject?->title,
            'image' => $class->image,
            'max_capacity' => $class->max_capacity,
            'start_date' => $class->date ? \Carbon\Carbon::parse($class->date)->format('j F') : null,
            'avg_rate' => (int)$class->ratings()->avg('rating') ?? 0,
            'all_rate' => $class->ratings()->count(),
            'rate' => $class->rate,
            'view_count' => (int)$class->view,
            'professor' => $class->professor->name,
            'time' => $class->time ? \Carbon\Carbon::parse($class->time)->format('H:i') : null,
            'professor_id' => $class->professor->id,
            'profile' => $class->professor->user->profile,
            'accent' => $class->language?->title,
            'age_group'=> $class->ageGroup?->title,
            'level'=> $class->languageLevel?->title,
            'subject'=> $class->subject?->title,
            'is_like' => $class->is_like,
            'platform'=> $class->platform?->title,
            'book'=> [
                'image' => $class->book->image,
                'name' => $class->book->name,
                'ageGroups' => $class->book->ageGroups?->first->title,
                'languageLevels' => $class->book->languageLevels?->first->title,
                'author' => $class->book->author,
                'edition' => $class->book->edition,
                'volume' => $class->book->volume,
                'topic'=>null,
            ],
        ];
        return api_response($return);

    }
    public function info($id)
    {
        $reserve = Webinar::find($id);
        $user  = auth()->user();
        $user_plan = UserPlan::where('user_id', $user->id)->where('is_active', 1)
            ->where('class_count','>',0)->where('expires_at', '>=', Carbon::now())
            ->whereRelation('plan' , 'plan_type' , 'webinar')->first();
        $days_left = null;
        $hours_left = null;
        if ($user_plan) {
            $now = Carbon::now();
            $end = Carbon::parse($user_plan->expires_at);
            $days_left = $now->diffInDays($end);
            $hours_left = $now->diffInHours($end);

        }

        $return = [
            'id' => $reserve->professor->id,
            'name' => $reserve->professor->name,
            'has_plan' => $user_plan ? true : false,
            'days_left' =>(int)$days_left ?? null ,
            'hours_left' => (int)$hours_left ?? null,
            'color' => $user_plan?->plan?->color ?? null,
            'plan_name' => $user_plan?->plan->name ?? null,
            'class_count' => $user_plan?->plan->class_count ?? null,
            'class_left' => $user_plan?->class_count ?? 0,
            'profile' => $reserve->professor->user->profile,
            'avg_rate' => (int)$reserve->professor->ratings()->avg('rating') ?? 0,
            'all_rate' => $reserve->professor->ratings()->count(),
            'rate' => $reserve->professor->rate,
        ];
        return api_response($return);
    }
    public function store(Request $request , $id)
    {
        $request->validate([
            'description' => 'nullable|string',
        ]);

        $userId = auth()->id();

        $exists = WebinarReservation::where('webinar_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already booked this class.'], 422);
        }

        $result = PlanHelper::reserveClass($userId, 'webinar', $request , $id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return api_response([],'success');
    }
    public function toggleLike(Request $request, $id)
    {
        $group = Webinar::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای لایک کردن لاگین کنید', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $group->id)
            ->where('likeable_type', Webinar::class)
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


        $groupClass = Webinar::findOrFail($id);

        $videoUrl = null;
        $voiceUrl = null;


        if ($request->hasFile('video_url')) {
            $file = $request->file('video_url');

            // مسیر در public اصلی
            $destinationPath = public_path('comment/video'); // public/comment/voices
            $fileName = time() . '_' . $file->getClientOriginalName();

            // انتقال فایل به مسیر
            $file->move($destinationPath, $fileName);

            $videoUrl = 'comment/video/' . $fileName; // مسیر برای ذخیره در دیتابیس یا ارسال به کلاینت
        }


        // ذخیره فایل صدا
        if ($request->hasFile('voice_url')) {
            $file = $request->file('voice_url');

            // مسیر در public اصلی
            $destinationPath = public_path('comment/voices'); // public/comment/voices
            $fileName = time() . '_' . $file->getClientOriginalName();

            // انتقال فایل به مسیر
            $file->move($destinationPath, $fileName);

            $voiceUrl = 'comment/voices/' . $fileName; // مسیر برای ذخیره در دیتابیس یا ارسال به کلاینت
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
        ], 'successes');
    }

    public function showComments(Request $request, $id)
    {
        $user_id = auth()->id();

        $groupClass = Webinar::findOrFail($id);


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

        $classGroup = Webinar::findOrFail($id);

        $existingRating = $classGroup->ratings()
            ->where('ratable_id', $classGroup->id)
            ->where('ratable_type', Webinar::class)
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


}
