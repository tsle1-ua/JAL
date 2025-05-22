<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('zip_code')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('type', ['apartamento', 'habitacion', 'casa', 'estudio']);
            $table->integer('bedrooms');
            $table->decimal('bathrooms', 3, 1);
            $table->date('available_from');
            $table->boolean('is_available')->default(true);
            $table->json('image_paths')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            // Índices para optimización
            $table->index(['city', 'is_available']);
            $table->index(['type', 'is_available']);
            $table->index(['price']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};