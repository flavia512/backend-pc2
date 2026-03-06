<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->after('id');
            $table->string('full_name')->after('email');
            $table->string('password_hash')->after('full_name');
            $table->integer('puntos')->default(0)->after('password_hash');
            $table->timestamp('last_login_at')->nullable()->after('puntos');
            $table->boolean('is_active')->default(true)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'full_name',
                'password_hash',
                'puntos',
                'last_login_at',
                'is_active'
            ]);
        });
    }
};
