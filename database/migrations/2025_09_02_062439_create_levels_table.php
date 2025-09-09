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
    Schema::create('levels', function (Blueprint $table) {
        $table->id();
        $table->text('code'); // Kode yang akan ditampilkan
        $table->json('correct_answer'); // Jawaban benar dalam format JSON (misal: {'x': 4, 'y': 3})
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
