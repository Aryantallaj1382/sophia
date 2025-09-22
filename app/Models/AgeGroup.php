<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    protected $fillable = ['title' , 'title_ch'];

    public function professors()
    {
        return $this->belongsToMany(Professor::class);
    }
}
