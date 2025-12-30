<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id', 'user_id', 'message', 'is_support_reply', 'file'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getFileAttribute($value)
    {
        return $value? url('public/'.$value) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
