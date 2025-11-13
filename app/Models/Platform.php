<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = ['title', 'icon'];

    public function professors()
    {
        return $this->belongsToMany(Professor::class);
    }
    public function getIconAttribute($value)
    {
        return $value ? url('public/'.$value): null;
    }
}
