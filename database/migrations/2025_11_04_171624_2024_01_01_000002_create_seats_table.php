<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('seats')) {
            Schema::create('seats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('studio_id')->constrained()->onDelete('cascade');
                $table->string('row');
                $table->integer('number');
                $table->string('seat_code');
                $table->enum('type', ['regular', 'sweetbox', 'disabled'])->default('regular');
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('seats');
    }
};
