<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->string('difficulty', 10)->default('easy')->after('code');
            $table->index('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropIndex(['difficulty']);
            $table->dropColumn('difficulty');
        });
    }
};
