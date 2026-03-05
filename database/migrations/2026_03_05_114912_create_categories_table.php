<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('discipline_id')->constrained('disciplines')->cascadeOnDelete();

            $table->string('name'); // Masculino, Femenino, Primera, Mixto...
            $table->string('format')->default('league'); // league|knockout
            $table->unsignedTinyInteger('team_size')->default(2); // 11 fútbol, 2 pádel
            $table->unsignedTinyInteger('min_players')->nullable(); // para validación roster
            $table->unsignedTinyInteger('max_players')->nullable();
            $table->json('rules')->nullable(); // puntos por victoria, desempates, etc.
            $table->timestamps();

            $table->unique(['discipline_id', 'name']);
            $table->index(['format']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
