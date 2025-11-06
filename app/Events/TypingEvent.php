<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $receiverId;

    public function __construct($userId, $receiverId)
    {
        $this->userId = $userId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        $ids = [$this->userId, $this->receiverId];
        sort($ids);
        $channelName = 'chat.1-4';

        \Log::info('📡 Broadcasting TypingEvent on channel:', [
            'channel' => $channelName,
            'from_user' => $this->userId,
            'to_user' => $this->receiverId,
        ]);

        return new PrivateChannel('chat.' . implode('-', $ids));
    }

    public function broadcastAs()
    {
        return 'typing';
    }

    public function broadcastWith()
    {
        return [
            'userId' => $this->userId,
            'receiverId' => $this->receiverId,
        ];
    }
}
