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
    Schema::create('films', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('genre')->nullable();
        $table->string('duration')->nullable();
        $table->integer('price')->default(0);
        $table->text('description');
        $table->string('image')->nullable();
        $table->enum('status', ['playing', 'upcoming', 'other'])->default('playing');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
