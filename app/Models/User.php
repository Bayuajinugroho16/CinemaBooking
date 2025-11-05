<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // TAMBAHKAN HasFactory di sini

    /**
     * Atribut yang bisa diisi massal (fillable)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // otomatis customer untuk pelanggan
    ];

    /**
     * Atribut yang disembunyikan saat serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutator untuk hash password otomatis
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
