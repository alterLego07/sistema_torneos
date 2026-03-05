<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('document')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable(); // M|F|X (o lo que definan)
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index(['last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
