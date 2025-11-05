<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('booking_seat')) {
            Schema::create('booking_seat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
                $table->foreignId('seat_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_seat');
    }
};
