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
        Schema::table('pengajuanjuduls', function (Blueprint $table) {
            $table->unsignedInteger('revisi_ke')->default(0)->after('catatan');
            $table->text('catatan_revisi_mahasiswa')->nullable()->after('revisi_ke');
            $table->timestamp('revisi_dikirim_pada')->nullable()->after('catatan_revisi_mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuanjuduls', function (Blueprint $table) {
            $table->dropColumn(['revisi_ke', 'catatan_revisi_mahasiswa', 'revisi_dikirim_pada']);
        });
    }
};
