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
        Schema::create('viaje_compartidos', function (Blueprint $table) {
        $table->id();

        $table->foreignId('driver_user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('route_id')
            ->constrained('rutas')
            ->cascadeOnDelete();

        $table->string('station_name')->nullable();
        $table->dateTime('trip_datetime');

        $table->unsignedInteger('seats_total');
        $table->unsignedInteger('seats_available');

        $table->string('status')->default('active');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viaje_compartidos');
    }
};
