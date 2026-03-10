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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('province', 100)->nullable()->after('avatar');
            $table->boolean('is_verified')->default(false)->after('province');
            $table->softDeletes();

            $table->index('province');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['province']);
            $table->dropIndex(['is_verified']);
            $table->dropColumn(['username', 'role', 'phone', 'avatar', 'province', 'is_verified', 'deleted_at']);
        });
    }
};
