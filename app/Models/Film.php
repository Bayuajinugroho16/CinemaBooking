<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
     use HasFactory;

    protected $fillable = [
        'title', 'genre', 'duration', 'price', 'description', 'image','status',
    ];

     public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
