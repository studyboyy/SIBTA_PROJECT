<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_ta', function (Blueprint $table) {
            $table->string('reviewer_markup_file')->nullable()->after('file');
            $table->timestamp('revisi_requested_at')->nullable()->after('reviewer_markup_file');
            $table->timestamp('revised_submitted_at')->nullable()->after('revisi_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_ta', function (Blueprint $table) {
            $table->dropColumn(['reviewer_markup_file', 'revisi_requested_at', 'revised_submitted_at']);
        });
    }
};
