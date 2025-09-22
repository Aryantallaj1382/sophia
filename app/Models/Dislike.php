<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Dislike extends Model
{


    protected $table = 'dislikes';

    protected $fillable = ['dislikable_type', 'dislikable_id','user_id'];
    protected $hidden = ['dislikable_type', 'dislikable_id', 'deleted_at'];

    /**
     * دریافت مدل مرتبط با لایک (پست یا کامنت و غیره).
     */
    public function likeable()
    {
        return $this->morphTo('dislikable', 'dislikable_type', 'dislikable_id');
    }

    public function dislikable()
    {
        return $this->morphTo();
    }

    /**
     * ارتباط با کاربر.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
