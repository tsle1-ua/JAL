<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('category')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->timestamps();

            // Índices para optimización
            $table->index(['city', 'category']);
            $table->index(['user_id']);
            $table->index(['is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
