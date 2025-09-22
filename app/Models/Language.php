<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['title','icon'];

    public function professors()
    {
        return $this->hasMany(Professor::class);
    }

}
