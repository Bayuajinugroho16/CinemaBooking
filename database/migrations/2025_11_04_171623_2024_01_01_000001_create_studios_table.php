<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('studios')) {
            Schema::create('studios', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('total_seats');
                $table->integer('rows');
                $table->integer('columns');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('studios');
    }
};
