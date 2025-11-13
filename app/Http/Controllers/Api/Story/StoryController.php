<?php

namespace App\Http\Controllers\Api\Story;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function show($id)
    {
        $story = Story::
            where('id', $id)
            ->firstOrFail();

        $user = $story->user;
        $user1 = auth()->user();

        $page = request('page', 1);

        $commentsQuery = Comment::with(['user', 'likes'])
            ->where('commentable_type', Story::class)
            ->where('commentable_id', $story->id);

        $comments = $commentsQuery->latest()->paginate(10, ['*'], 'page', $page);
            $formattedStory = [
                'id' => $story->id,
                'image' => $story->cover_image,
                'video' => $story->video,
                'title' => $story->title,
                'professor'=>[
                    'name' => $story->professor?->name,
                    'profile' => $story->professor?->user->profile,
                ],
                'likes_count' => $story->likes()->count(),
                'is_liked' => $user1 ? $story->likes()->where('user_id', $user1->id)->exists() : false,
                'comments_count' => $comments->total(), // تعداد کل کامنت‌ها
                'comments' => $comments->map(function ($comment) use ($user1) {
                    return [
                        'id' => $comment->id,
                        'name' => $comment->user->name,
                        'body' => $comment->body,
                        'profile_image' => $comment->user->profile,
                        'likes_count' => $comment->likes()->count(),
                        'is_liked' => $user1 ? $comment->likes()->where('user_id', $user1->id)->exists() : false,
                        'is_mine' => $user1 ? $comment->user_id == $user1->id : false,
                        'created_at' => $comment->created_at
                    ];
                }),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ],
            ];

        return api_response( $formattedStory);
    }

    public function toggleLike(Request $request, $id)
    {
        $group = Story::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای لایک کردن لاگین کنید', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $group->id)
            ->where('likeable_type', Story::class)
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
            'body' => 'nullable|string|min:1|max:400',
        ], [], [
            'body' => 'توضیحات',
        ]);
        $groupClass = Story::findOrFail($id);
        $comment = $groupClass->comments()->create([
            'user_id' => auth()->user()->id,
            'body' => $request->body,

        ]);

        return api_response([
            'id' => $comment->id,
            'body' => $comment->body,
            'created_at' => $comment->created_at->diffForHumans(),
        ], 'نظر با موفقیت ثبت شد');
    }

}
