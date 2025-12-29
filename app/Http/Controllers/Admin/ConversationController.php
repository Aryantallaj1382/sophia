<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::
             latest()
            ->get();

        return view('admin.conversations.index', compact('conversations'));
    }

    // مشاهده پیام‌های یک چت
    public function show($id)
    {
        $conversation = Conversation::findOrFail($id);
        $user = auth()->user()->id;

        return view('admin.conversations.show', compact(['conversation','user']));
    }
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->delete();
        return redirect()->route('admin.conversations.index');
    }
}
