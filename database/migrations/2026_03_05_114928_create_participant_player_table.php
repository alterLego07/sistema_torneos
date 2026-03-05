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
        Schema::create('participant_player', function (Blueprint $table) {
            $table->foreignUlid('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignUlid('player_id')->constrained('players')->cascadeOnDelete();

            $table->string('role')->nullable(); // captain, coach, etc.
            $table->timestamps();

            $table->primary(['participant_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_player');
    }
};
