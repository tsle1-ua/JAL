<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_info', function (Blueprint $table) {
            $table->id();
            $table->string('university_name');
            $table->string('degree_name');
            $table->decimal('cut_off_mark', 4, 2)->nullable();
            $table->integer('year');
            $table->string('scholarship_name')->nullable();
            $table->text('scholarship_description')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('link')->nullable();
            $table->enum('type', ['notas-corte', 'beca', 'general'])->default('general');
            $table->timestamps();

            // Índices para optimización
            $table->index(['university_name', 'year']);
            $table->index(['degree_name']);
            $table->index(['type']);
            $table->index(['application_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_info');
    }
};