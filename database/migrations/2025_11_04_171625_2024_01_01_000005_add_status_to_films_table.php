<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('films', function (Blueprint $table) {
            if (!Schema::hasColumn('films', 'status')) {
                $table->enum('status', ['playing', 'upcoming', 'other'])->default('playing');
            }
        });
    }

    public function down()
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
