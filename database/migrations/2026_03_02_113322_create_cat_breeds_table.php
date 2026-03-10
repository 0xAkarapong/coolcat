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
        Schema::create('cat_breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_th', 100)->nullable();
            $table->string('origin', 100)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_breeds');
    }
};
