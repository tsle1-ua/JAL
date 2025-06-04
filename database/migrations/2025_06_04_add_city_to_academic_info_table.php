<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_info', function (Blueprint $table) {
            $table->string('city')->nullable()->after('university_name');
        });
    }

    public function down(): void
    {
        Schema::table('academic_info', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};
