<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // ← Biarkan ini saja
    ];

    // HAPUS mutator ini ↓
    // public function setPasswordAttribute($password)
    // {
    //     $this->attributes['password'] = Hash::make($password);
    // }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'user';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
