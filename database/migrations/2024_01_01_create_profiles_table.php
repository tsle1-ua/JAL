<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->enum('gender', ['masculino', 'femenino', 'no-binario', 'prefiero-no-decir'])->nullable();
            $table->integer('age')->nullable();
            $table->enum('smoking_preference', ['fumador', 'no-fumador', 'flexible'])->nullable();
            $table->enum('pet_preference', ['tiene-mascotas', 'le-gustan-mascotas', 'no-mascotas', 'flexible'])->nullable();
            $table->integer('cleanliness_level')->nullable()->comment('Escala 1-5');
            $table->enum('sleep_schedule', ['madrugador', 'noctambulo', 'flexible'])->nullable();
            $table->json('hobbies')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('major')->nullable();
            $table->string('university_name')->nullable();
            $table->boolean('looking_for_roommate')->default(false);
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};