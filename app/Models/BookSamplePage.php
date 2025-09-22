<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookSamplePage extends Model
{
    protected $fillable = ['book_id', 'image'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function getImageAttribute($value)
    {
        return $value ? url('public/'.$value) : null;
    }
}
