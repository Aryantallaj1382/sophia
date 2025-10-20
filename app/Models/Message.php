<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function getFilePathAttribute($value)
    {
        return $value ? url('public/'.$value) : null;

    }
    public function getVoicePathAttribute($value)
    {
        return $value ? url('public/'.$value) : null;

    }

}
