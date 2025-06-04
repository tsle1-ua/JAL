<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leisure_zone_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leisure_zone_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['leisure_zone_id', 'user_id']);
            $table->index(['rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leisure_zone_reviews');
    }
};
