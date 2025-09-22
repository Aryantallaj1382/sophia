<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name'];

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'professor_skill');
    }
}
