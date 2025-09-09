<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('player_id');
            $table->index('score');
            $table->index(['player_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('game_plays', function (Blueprint $table) {
            $table->dropIndex(['game_plays_created_at_index']);
            $table->dropIndex(['game_plays_player_id_index']);
            $table->dropIndex(['game_plays_score_index']);
            $table->dropIndex(['game_plays_player_id_created_at_index']);
        });
    }
};
