<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->dropColumn('category');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('category', [
                'academico', 'cultural', 'deportivo', 'social', 'musical',
                'gastronomico', 'tecnologico', 'artistico', 'voluntariado'
            ])->default('social');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
