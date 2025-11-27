<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('showtime_seat', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained()->onDelete('cascade');
        $table->foreignId('seat_id')->constrained()->onDelete('cascade');
        $table->foreignId('film_id')->constrained()->onDelete('cascade');
        $table->date('show_date');
        $table->time('show_time');
        $table->boolean('is_available')->default(true);
        $table->timestamps();

        // Unique constraint untuk mencegah double booking
        $table->unique(['seat_id', 'film_id', 'show_date', 'show_time']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtime_seat');
    }
};
