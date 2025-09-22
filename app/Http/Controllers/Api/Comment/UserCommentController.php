<?php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
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
                    'image' => $item->commentable?->image ?? null,
                ];
            });
        }
        if($type == 'video')
        {
            $comment = Comment::where('user_id', $user->id)->whereNotNull('video_url')->paginate();
            $comment->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,

                    'voice' => $item->voice_url,
                    'status' => $item->admin_status,
                    'type' => $item->type,
                    'image' => $item->commentable?->image ?? null,
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
                    'image' => $item->commentable?->image ?? null,
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
}
