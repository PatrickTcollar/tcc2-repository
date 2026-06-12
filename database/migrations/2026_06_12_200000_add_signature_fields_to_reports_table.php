<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('signed_by')->nullable()->after('report_content');
            $table->string('signer_crf')->nullable()->after('signed_by');
            $table->timestamp('signed_at')->nullable()->after('signer_crf');
            $table->longText('signature_image')->nullable()->after('signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['signed_by', 'signer_crf', 'signed_at', 'signature_image']);
        });
    }
};
