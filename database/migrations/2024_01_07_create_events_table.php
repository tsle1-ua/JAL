<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->time('time')->nullable();
            $table->datetime('end_datetime')->nullable();
            $table->foreignId('place_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->enum('category', [
                'academico', 'cultural', 'deportivo', 'social', 'musical', 
                'gastronomico', 'tecnologico', 'artistico', 'voluntariado'
            ])->default('social');
            $table->boolean('is_public')->default(true);
            $table->decimal('price', 8, 2)->default(0.00);
            $table->integer('max_attendees')->nullable();
            $table->integer('current_attendees')->default(0);
            $table->timestamps();

            // Índices para optimización
            $table->index(['date', 'is_public']);
            $table->index(['category', 'is_public']);
            $table->index(['user_id']);
            $table->index(['place_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};