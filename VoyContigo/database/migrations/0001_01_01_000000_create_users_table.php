<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Equivale a tu INT AUTO_INCREMENT PRIMARY KEY
            $table->string('email')->unique();
            $table->string('full_name');
            $table->string('password_hash'); 
            $table->integer('puntos')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // Crea automáticamente created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};