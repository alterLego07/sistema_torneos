<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_registrations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignUlid('participant_id')->constrained('participants')->cascadeOnDelete();

            $table->string('status')->default('active'); // active|inactive|disqualified
            $table->unsignedSmallInteger('seed')->nullable(); // para brackets / orden de fixture
            $table->integer('points_adjustment')->default(0); // sanciones/bonus
            $table->timestamps();

            $table->unique(['category_id', 'participant_id']);
            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_registrations');
    }
};
