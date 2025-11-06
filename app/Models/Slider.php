<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['image', 'link' , 'mobile_image'];
    public function getMobileImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
}

