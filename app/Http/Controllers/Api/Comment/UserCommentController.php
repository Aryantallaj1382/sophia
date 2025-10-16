<?php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Professor;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class UserCommentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $user = auth()->user();

        $comment = [];
        if($type == 'text')
        {
            $comment = Comment::where('user_id', $user->id)->whereNotNull('body')->paginate();
            $comment->getCollection()->transform(function ($item) {
                return [
                    'body' => $item->body,
                    'id' => $item->id,
                    'status' => $item->admin_status,
                    'type' => $item->type,
                    'image' => $item->commentable?->image ?? $item->commentable?->user?->profile?? null,
                    'title' =>$item->commentable?->title ?? $item->commentable?->name ?? $item->commentable?->name?? null,
                ];
            });
        }
        if($type == 'video')
        {
            $comment = Comment::where('user_id', $user->id)->whereNotNull('video_url')->paginate();
            $comment->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,

                    'video_url' => $item->video_url,
                    'status' => $item->admin_status,
                    'type' => $item->type,
                    'image' => $item->commentable?->image ?? $item->commentable?->user?->profile?? null,
                    ];
            });
        }

        if($type == 'voice')
        {
            $comment = Comment::where('user_id', $user->id)->whereNotNull('voice_url')->paginate();
           $comment->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,

                    'voice_url' => $item->voice_url,
                    'status' => $item->admin_status,
                    'type' => $item->type,
                    'image' => $item->commentable?->image ?? $item->commentable?->user?->profile?? null,
                ];
            });
        }
        return api_response($comment);
    }

    public function delete($id)
    {
        $user = auth()->user();
        $comment = Comment::where('user_id', $user->id)->find($id);
        $comment->delete();
        return api_response([], 'deleted');


    }
    public function update(Request $request,$id)
    {
        $user = auth()->user();
        $comment = Comment::where('user_id', $user->id)->find($id);
        $comment->body = $request->input('body');
        $comment->save();
        return api_response([], 'updated');

    }


    public function professors_comments(Request $request)
    {
        $professors = Professor::whereHas('comments', function ($q) {
            $q->whereNotNull('body');
        })->paginate();

        $professors->getCollection()->transform(function ($item) {
            $comments = $item->comments()->whereNotNull('body')->latest()->take(3)->get();
            $accents = $item->accents()->get()->map(fn($accent) => $accent->title)->toArray();

            return [
                'id' => $item->id,
                'name' => $item->name,
                'profile' => $item->user->profile,
                'is_verified' => $item->is_verified,
                'avg_rate' => (int)$item->ratings()->avg('rating') ?? 0,
                'all_rate' => $item->ratings()->count(),
                'rate' => $item->rate ?? 0,
                'accents' => $accents,
                'comments' => $comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'profile' => $comment->user->profile,
                    'name' => $comment->user->name,
                    'created_at' => $comment?->created_at?->format('d M Y , H:i'),
                ]),
            ];
        });

        return api_response($professors);
    }

    public function professors_comments_audio(Request $request)
    {
        $professors = Professor::whereHas('comments', function ($q) {
            $q->whereNotNull('voice_url');
        })->paginate();

        $professors->getCollection()->transform(function ($item) {
            $comments = $item->comments()->whereNotNull('voice_url')->latest()->take(3)->get();
            $accents = $item->accents()->get()->map(fn($accent) => $accent->title)->toArray();

            return [
                'id' => $item->id,
                'name' => $item->name,
                'profile' => $item->user->profile,
                'is_verified' => $item->is_verified,
                'avg_rate' => (int)$item->ratings()->avg('rating') ?? 0,
                'all_rate' => $item->ratings()->count(),
                'rate' => $item->rate?? 0,
                'accents' => $accents,
                'comments' => $comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'voice' => $comment->voice_url,
                    'profile' => $comment->user->profile,
                    'name' => $comment->user->name,
                    'created_at' => $comment?->created_at?->format('d M Y , H:i'),
                ]),
            ];
        });

        return api_response($professors);
    }

    public function professors_comments_video(Request $request)
    {
        $professors = Professor::whereHas('comments', function ($q) {
            $q->whereNotNull('video_url');
        })->paginate();

        $professors->getCollection()->transform(function ($item) {
            $comments = $item->comments()->whereNotNull('video_url')->latest()->take(3)->get();
            $accents = $item->accents()->get()->map(fn($accent) => $accent->title)->toArray();

            return [
                'id' => $item->id,
                'name' => $item->name,
                'profile' => $item->user->profile,
                'is_verified' => $item->is_verified,
                'avg_rate' => (int)$item->ratings()->avg('rating') ?? 0,
                'all_rate' => $item->ratings()->count(),
                'rate' => $item->rate ?? 0,
                'accents' => $accents,
                'comments' => $comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'video_url' => $comment->video_url,
                    'profile' => $comment->user->profile,
                    'name' => $comment->user->name,
                    'created_at' => $comment?->created_at?->format('d M Y , H:i'),
                ]),
            ];
        });

        return api_response($professors);
    }


}
