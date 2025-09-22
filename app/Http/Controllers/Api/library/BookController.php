<?php

namespace App\Http\Controllers\Api\library;

use App\Http\Controllers\Controller;
use App\Models\Accent;
use App\Models\AgeGroup;
use App\Models\Book;
use App\Models\LanguageLevel;
use App\Models\LearningSubgoal;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::filter($request->all())->paginate(12);

        $books->getCollection()->transform(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->name,
                'image' => $book->image,
                'ageGroups' => $book->ageGroups?->first()->title ?? null,



            ];
        });
        return api_response($books);
    }
    public function show(Request $request,$id)
    {


        $lang = $request->header('X-Language', 'en');

        $book = Book::with(['professors.user', 'samplePages'])->findOrFail($id);
        $book->increment('view_count');

        $other_volume = Book::where('name','like', "%{$book->name}%")
            ->where('id', '!=', $id)->where('volume','!=',$book->volume)->take(5)->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'image' => $item->image,
                    'volume' => $item->volume,

                ];
            });
        $other_edition = Book::where('name','like', "%{$book->name}%")
            ->where('id', '!=', $id)->where('volume','=',$book->volume)->where('edition','!=',$book->editions)->take(5)->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'image' => $item->image,
                    'edition' => $item->edition,

                ];
            });
        $return = [
            'id' => $book->id,
            'title' => $book->name,
            'image' => $book->image,
            'ageGroups' => $book->ageGroups->map(function($item) use ($lang) {
                return match ($lang) {
                    'zh' => $item->title_ch ?? $item->title,
                    default => $item->title,
                };
            })->toArray(),

            'languageLevels' => $book->languageLevels->map(function($item) use ($lang) {
                return match ($lang) {
                    'zh' => $item->title_ch ?? $item->title,
                    default => $item->title,
                };
            })->toArray(),
            'type' => $book->title_file,
            'author' => $book->author,
            'edition' => $book->edition,
            'volume' => $book->volume,
            'topics' => $book->topics,
            'description' => $book->description,
            'file' => $book->file,
            'is_like' => $book->is_like,
            'video_file' => $book->file,
            'avg_rate' =>(int)$book->ratings()->avg('rating') ?? 0,
            'all_rate' =>$book->ratings()->count(),
            'rate' =>$book->rate ?? 0,
            'view_count' =>(int)$book->view_count,
            'audio_file' => $book->file,
            'video' => $book->video,
            'video_cover' => $book->video_cover,
            'professors' => $book->professors->map(function($professor) {
                return [
                    'id' => $professor->id,
                    'name' => $professor->user->name ,
                    'is_verified' => $professor->is_verified ,
                    'image' => $professor->user->profile ?? null,
                ];
            }),
            'sample_pages' => $book->samplePages->pluck('image')->toArray(),
            'other_edition' => $other_edition,
            'other_volume' =>$other_volume,

        ];
        return api_response($return);
    }
    public function getFilters(Request $request)
    {
        $lang = $request->header('X-Language', 'en');

        $accents = Accent::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name'  => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });

        $learningSubgoal = LearningSubgoal::with(['goal'])
            ->get()
            ->groupBy(function ($learningGoal) use ($lang) {
                $goal = $learningGoal->goal;
                if (!$goal) return null;
                return $lang === 'zh' ? ($goal->title_ch ?? $goal->title) : $goal->title;
            })
            ->map(function ($learningGoalsGroup) use ($lang) {
                $firstGoal = $learningGoalsGroup->first()->goal;

                return [
                    'goal_id' => $firstGoal->id,
                    'goal_title' => $lang === 'zh' ? ($firstGoal->title_ch ?? $firstGoal->title) : $firstGoal->title,
                    'subgoals' => $learningGoalsGroup->map(function ($learningGoal) use ($lang) {
                        return [
                            'value' => $learningGoal->id,
                            'name'  => $lang === 'zh' ? ($learningGoal->title_ch ?? $learningGoal->title) : $learningGoal->title,
                        ];
                    })->values(),
                ];
            })
            ->values()
            ->toArray();

        $languageLevel = LanguageLevel::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name'  => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });

        $ageGroup = AgeGroup::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name'  => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });
        $type = Book::select('title_file')
            ->distinct()
            ->pluck('title_file')
            ->filter() // حذف null ها
            ->map(fn($item) => [
                'value' => $item,
                'name' => $item,
            ])
            ->values()
            ->toArray();

        return response()->json([
            'accents'         => $accents,
            'age_groups'      => $ageGroup,
            'language_levels' => $languageLevel,
            'learning_goals'  => $learningSubgoal,
            'title_file'      => $type,
        ]);
    }
    public function toggleLike(Request $request, $id)
    {
        $group = Book::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'Please log in to like', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $group->id)
            ->where('likeable_type', Book::class)
            ->first();

        if ($like) {
            $like->delete();
            return api_response([
                'is_like' => false,
            ], 'dislike');
        }

        $group->likes()->create([
            'user_id' => $user->id,
        ]);

        return api_response([
            'is_like' => true,

        ], 'like');
    }
    public function comment(Request $request, $id)
    {

        $request->validate([
            'body' => 'nullable|string|min:1|max:400',
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


        $groupClass = Book::findOrFail($id);

        $videoUrl = null;
        $voiceUrl = null;

        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('comment/videos', 'public');
            $videoUrl = $videoPath;
        }

        if ($request->hasFile('voice_url')) {
            $voicePath = $request->file('voice_url')->store('comment/voices', 'public');
            $voiceUrl = $voicePath;
        }

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

        $groupClass = Book::findOrFail($id);


        $query = $groupClass->comments()->with([
            'user',
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

        $classGroup = Book::findOrFail($id);

        $existingRating = $user->ratings()
            ->where('ratable_id', $classGroup->id)
            ->where('ratable_type', Book::class)
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
