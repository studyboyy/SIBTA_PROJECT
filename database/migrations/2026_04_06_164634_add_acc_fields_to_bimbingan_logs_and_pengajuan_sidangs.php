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
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->timestamp('acc_at')->nullable()->after('status');
        });

        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->text('catatan_dosen')->nullable()->after('catatan_admin');
            $table->timestamp('acc_kelayakan_at')->nullable()->after('catatan_dosen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->dropColumn('acc_at');
        });

        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->dropColumn(['catatan_dosen', 'acc_kelayakan_at']);
        });
    }
};
