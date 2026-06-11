<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dateTime('upload_date')->change();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dateTime('generation_date')->change();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dateTime('birth_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->date('upload_date')->change();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->date('generation_date')->change();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->change();
        });
    }
};
