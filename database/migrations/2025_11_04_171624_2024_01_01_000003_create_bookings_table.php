<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('film_id')->constrained()->onDelete('cascade');
                $table->foreignId('studio_id')->constrained()->onDelete('cascade');
                $table->date('show_date');
                $table->time('show_time');
                $table->integer('total_seats');
                $table->decimal('total_price', 10, 2);
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
