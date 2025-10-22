<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $userId;
    public $isTyping; // ⭐ اضافه شده
    public $otherUserId;

    public function __construct($conversationId, $userId, $otherUserId, $isTyping = false)
    {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->otherUserId = $otherUserId;
        $this->isTyping = $isTyping; // ⭐
    }

    public function broadcastOn()
    {
        return new Channel('chat-updates.' . $this->userId);
    }

    public function broadcastWith()
    {
        $conversation = Conversation::find($this->conversationId);

        $otherUser = $this->otherUserId == $conversation->user1->id
            ? $conversation->user1
            : $conversation->user2;
        $lastMessage = $conversation->messages->last();
        $lastMessageText = match ($lastMessage->message_type) {
            'text' => $lastMessage->message,
            'file' => 'فایل',
            'voice' => 'فایل صوتی',
            default => 'پیام',
        };


        return [
            'id' => $otherUser->id,
            'user_name' => $otherUser->first_name,
            'user_image' => $otherUser->profile,
            'last_message' => $lastMessageText,
            'last_message_time' => $lastMessage?->created_at->timezone('Asia/Tehran')->format('H:i'),
            'is_typing' => $this->isTyping ?true : false,
        ];
    }

    public function broadcastAs()
    {
        return 'chat.updated';
    }
}
