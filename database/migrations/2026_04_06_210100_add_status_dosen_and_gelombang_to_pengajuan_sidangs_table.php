<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->string('status_dosen')->default('pending')->after('status');
            $table->unsignedInteger('gelombang')->nullable()->after('status_dosen');
            $table->timestamp('diproses_admin_pada')->nullable()->after('acc_kelayakan_at');
        });

        DB::table('pengajuan_sidangs')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->get()
            ->each(function ($row) {
                DB::table('pengajuan_sidangs')
                    ->where('id', $row->id)
                    ->update(['status_dosen' => $row->status ?: 'pending']);
            });
    }

    public function down(): void
    {
        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->dropColumn(['status_dosen', 'gelombang', 'diproses_admin_pada']);
        });
    }
};
