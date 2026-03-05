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
        Schema::create('matches', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('category_id')->constrained('categories')->cascadeOnDelete();

            $table->unsignedSmallInteger('round')->nullable(); // jornada o ronda
            $table->unsignedSmallInteger('match_number')->nullable();

            $table->foreignUlid('home_participant_id')->constrained('participants')->restrictOnDelete();
            $table->foreignUlid('away_participant_id')->constrained('participants')->restrictOnDelete();

            $table->timestamp('scheduled_at')->nullable();
            $table->string('venue')->nullable(); // cancha/campo
            $table->string('status')->default('scheduled'); // scheduled|played|canceled|walkover
            $table->timestamps();

            $table->index(['category_id', 'scheduled_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
