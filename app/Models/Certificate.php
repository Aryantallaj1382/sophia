<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFileAttribute($value)
    {
        return $value ? url("public/" . $value) : null;

    }
}
