<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{

    protected $fillable = ['likeable_type', 'likeable_id','user_id','likeable_type', 'likeable_id', 'deleted_at'];

    /**
     * دریافت مدل مرتبط با لایک (پست یا کامنت و غیره).
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * ا
     * رتباط با کاربر.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
