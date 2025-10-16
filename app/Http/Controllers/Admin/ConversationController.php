<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['user1', 'user2', 'messages.sender'])
            ->latest()
            ->get();

        return view('admin.conversations.index', compact('conversations'));
    }

    // مشاهده پیام‌های یک چت
    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'user1', 'user2'])
            ->findOrFail($id);

        return view('admin.conversations.show', compact('conversation'));
    }
}
