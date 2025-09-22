<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile', // اضافه شد

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    // app/Models/User.php
    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    public function getProfileAttribute($value)
    {
       return $value ? url('public/'.$value) : null;
    }
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function userPlans()
    {
        return $this->hasMany(UserPlan::class);
    }
    public function notifications()
    {
        return $this->hasMany(\App\Models\UserNotification::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
