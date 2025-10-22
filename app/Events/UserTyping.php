<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $user1Id;
    public $user2Id;

    public function __construct($userId, $user1Id, $user2Id)
    {
        $this->userId = $userId;
        // ترتیب ثابت: ID کوچکتر به‌عنوان user1Id و ID بزرگتر به‌عنوان user2Id
        $this->user1Id = min($user1Id, $user2Id);
        $this->user2Id = max($user1Id, $user2Id);
    }

    public function broadcastOn()
    {
        return new Channel("chat.{$this->user1Id}-{$this->user2Id}");
    }

    public function broadcastAs()
    {
        return 'typing';
    }
}
