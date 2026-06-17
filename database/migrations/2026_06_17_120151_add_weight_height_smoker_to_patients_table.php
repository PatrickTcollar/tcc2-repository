<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Converte smoker de boolean para string
        DB::statement("ALTER TABLE patients ALTER COLUMN smoker TYPE VARCHAR(20) USING CASE WHEN smoker THEN 'sim' ELSE 'nao' END");
        DB::statement("ALTER TABLE patients ALTER COLUMN smoker SET DEFAULT 'nao'");

        Schema::table('patients', function (Blueprint $table) {
            $table->decimal('weight', 5, 2)->nullable()->after('smoker'); // kg
            $table->decimal('height', 5, 2)->nullable()->after('weight'); // cm
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['weight', 'height']);
        });

        DB::statement("ALTER TABLE patients ALTER COLUMN smoker TYPE BOOLEAN USING CASE WHEN smoker = 'sim' THEN TRUE ELSE FALSE END");
        DB::statement("ALTER TABLE patients ALTER COLUMN smoker SET DEFAULT FALSE");
    }
};
