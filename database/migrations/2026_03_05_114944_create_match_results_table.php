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
        Schema::create('match_results', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('match_id')->constrained('matches')->cascadeOnDelete();

            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();

            $table->json('details')->nullable(); // sets, penales, tarjetas, etc.
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->unique(['match_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
