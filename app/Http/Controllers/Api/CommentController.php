<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Dislike;
use App\Models\Like;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function toggleLike(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای لایک کردن لاگین کنید', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $comment->id)
            ->where('likeable_type', Comment::class)
            ->where('user_id',$user->id)
            ->first();

        if ($like) {
            $like->delete();
            return api_response(['is_like' => false], 'لایک حذف شد');
        }

        $comment->likes()->create([
            'user_id' => $user->id,
        ]);

        return api_response(['is_like' => true], 'لایک شد');
    }


    public function getUserComments(Request $request)
    {
        $user = auth()->user();

        $query = Comment::with('commentable')
            ->where('user_id', $user->id);
        $type = null;
        if ($request->has('type')) {
            switch ($request->type) {
                case 'text':
                    $query->whereNotNull('body')
                        ->whereNull('voice_url')
                        ->whereNull('video_url');
                    $type = 'text';
                    break;
                case 'voice':

                    $query->whereNotNull('voice_url');
                    $type = 'voice';

                    break;
                case 'video':
                    $query->whereNotNull('video_url');
                    $type = 'video';

                    break;
            }
        }

        $comments = $query->latest()->paginate();
        $comments->getCollection()->transform(function ($comment) use ($type){
            if (auth()->check()) {
                $is_like = Like::where('user_id', auth()->id())
                    ->where('likeable_id', $comment->id)
                    ->where('likeable_type', Comment::class)
                    ->exists();
                $is_dislike = Dislike::where('user_id', auth()->id())
                    ->where('dislikable_id', $comment->id)
                    ->where('dislikable_type', Comment::class)
                    ->exists();
            }
            return [
                'id' => $comment->id,
                'likes_count'=> $comment->likes->count(),
                'is_like'=> $is_like ?? null,
                'is_dislike'=> $is_dislike ?? null,
                'dislikes_count'=> $comment->dislikes->count(),
                'type' => $type,
                'content' => $comment->body ?? $comment->voice_url ?? $comment->video_url,
                'status' => $comment->admin_status ,
                'commented_on_type' => class_basename($comment->commentable_type),
                'commented_on' => $comment->commentable ? $comment->commentable->title ?? $comment->commentable->name ?? $comment->commentable->subject->title ?? $comment->commentable->full_name()?? '---' : '---',
                'image' => $comment->commentable ? $comment->commentable->image ?? $comment->commentable->first_image ?? api::api.$comment->commentable->profile ??null : null,
                'id_on' => $comment->commentable ? $comment->commentable->id ?? $comment->commentable->id ?? '---' : '---',
                'created_at' => $comment->created_at ?$comment->created_at->format('Y-m-d H:i') : null,
            ];
        });

        return api_response($comments);
    }
    public function delete($id)
    {
        $user = auth()->user();
        Comment::where('user_id' , $user->id)->where('id',$id)->delete();
        return api_response([],'با موفقیت کامنت حذف شد');
    }

    public function update($id)
    {
        $user = auth()->user();
        Comment::where('user_id' , $user->id)->where('id',$id)->whereNotNull('body')->update([
            'body' => request()->body,
            'admin_status' => 'pending'
        ]);
        api_response([],'با موفقیت کامنت ویرایش شد');
    }
}
