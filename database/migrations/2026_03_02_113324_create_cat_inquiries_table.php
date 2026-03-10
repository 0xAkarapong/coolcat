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
        Schema::create('cat_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('cat_listings')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->date('meet_date')->nullable();
            $table->time('meet_time')->nullable();
            $table->string('meet_location')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('seller_note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['listing_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_inquiries');
    }
};
