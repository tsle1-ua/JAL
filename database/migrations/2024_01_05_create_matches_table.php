<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_1')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id_2')->constrained('users')->onDelete('cascade');
            $table->enum('user_1_status', ['liked', 'disliked', 'pending'])->default('pending');
            $table->enum('user_2_status', ['liked', 'disliked', 'pending'])->default('pending');
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();

            // Asegurar que no haya duplicados para el mismo par de usuarios
            $table->unique(['user_id_1', 'user_id_2']);
            
            // Índices para optimización
            $table->index(['user_id_1', 'user_1_status']);
            $table->index(['user_id_2', 'user_2_status']);
            $table->index(['matched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};