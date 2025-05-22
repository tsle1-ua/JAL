<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('desired_university')->nullable();
            $table->string('desired_degree_type')->nullable();
            $table->string('current_academic_year')->nullable();
            $table->json('interests')->nullable(); // Array de intereses académicos
            $table->boolean('scholarship_notifications')->default(true);
            $table->boolean('cut_off_notifications')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_preferences');
    }
};