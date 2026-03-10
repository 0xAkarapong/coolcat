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
        Schema::create('cat_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('cat_breeds')->nullOnDelete();
            $table->string('name', 100);
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->date('birthdate')->nullable();
            $table->string('color', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('type', ['adoption', 'sale'])->default('sale');
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['active', 'reserved', 'sold', 'closed'])->default('active');
            $table->boolean('is_neutered')->default(false);
            $table->boolean('is_vaccinated')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->string('province', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('province');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_listings');
    }
};
