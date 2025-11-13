<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    protected $table = 'conversation_users';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

}
