<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE patients ALTER COLUMN smoker TYPE VARCHAR(20) USING CASE WHEN smoker THEN 'sim' ELSE 'nao' END");
            DB::statement("ALTER TABLE patients ALTER COLUMN smoker SET DEFAULT 'nao'");
        } else {
            // MySQL: MODIFY converts tinyint 0→'0', 1→'1' then we remap
            DB::statement("ALTER TABLE patients MODIFY COLUMN smoker VARCHAR(20) NOT NULL DEFAULT 'nao'");
            DB::statement("UPDATE patients SET smoker = 'nao' WHERE smoker = '0'");
            DB::statement("UPDATE patients SET smoker = 'sim' WHERE smoker = '1'");
        }

        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'weight')) {
                $table->decimal('weight', 5, 2)->nullable()->after('smoker');
            }
            if (!Schema::hasColumn('patients', 'height')) {
                $table->decimal('height', 5, 2)->nullable()->after('weight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(array_filter(['weight', 'height'], fn($col) => Schema::hasColumn('patients', $col)));
        });

        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE patients ALTER COLUMN smoker TYPE BOOLEAN USING CASE WHEN smoker = 'sim' THEN TRUE ELSE FALSE END");
            DB::statement("ALTER TABLE patients ALTER COLUMN smoker SET DEFAULT FALSE");
        } else {
            DB::statement("UPDATE patients SET smoker = '1' WHERE smoker = 'sim'");
            DB::statement("UPDATE patients SET smoker = '0' WHERE smoker != '1'");
            DB::statement("ALTER TABLE patients MODIFY COLUMN smoker TINYINT(1) NOT NULL DEFAULT 0");
        }
    }
};
