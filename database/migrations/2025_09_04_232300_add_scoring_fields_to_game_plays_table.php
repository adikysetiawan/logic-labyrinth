<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->unsignedInteger('time_seconds')->default(0)->after('success');
            $table->unsignedSmallInteger('base_points')->default(0)->after('time_seconds');
            $table->decimal('score', 8, 2)->default(0)->after('base_points');
        });
    }

    public function down(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->dropColumn(['time_seconds', 'base_points', 'score']);
        });
    }
};
