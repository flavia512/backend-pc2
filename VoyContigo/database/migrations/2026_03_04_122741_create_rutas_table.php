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
        Schema::create('rutas', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->string('origin_text');
        $table->decimal('origin_lat', 10, 7);
        $table->decimal('origin_lng', 10, 7);

        $table->string('dest_text');
        $table->decimal('dest_lat', 10, 7);
        $table->decimal('dest_lng', 10, 7);

        $table->time('arrival_time')->nullable();
        $table->unsignedInteger('duration_min')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
