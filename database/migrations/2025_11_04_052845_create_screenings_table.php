<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('screenings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('film_id')->constrained()->cascadeOnDelete();
        $table->date('date');
        $table->time('time');
        $table->integer('total_seats')->default(60);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
