<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('bimbingans', function (Blueprint $table) {
                $table->dropUnique('bimbingans_mahasiswa_id_unique');
            });
        } catch (Throwable) {
            // The index may not exist on fresh databases or different drivers.
        }

        DB::table('bimbingans')
            ->whereNull('peran')
            ->orWhere('peran', '')
            ->orWhere('peran', 'pembimbing')
            ->update(['peran' => 'pembimbing_1']);

        Schema::table('bimbingans', function (Blueprint $table) {
            $table->unique(['mahasiswa_id', 'dosen_id'], 'bimbingans_mahasiswa_dosen_unique');
            $table->unique(['mahasiswa_id', 'peran'], 'bimbingans_mahasiswa_peran_unique');
        });
    }

    public function down(): void
    {
        Schema::table('bimbingans', function (Blueprint $table) {
            $table->dropUnique('bimbingans_mahasiswa_dosen_unique');
            $table->dropUnique('bimbingans_mahasiswa_peran_unique');
        });

        DB::table('bimbingans')
            ->where('peran', 'pembimbing_1')
            ->update(['peran' => 'pembimbing']);

        Schema::table('bimbingans', function (Blueprint $table) {
            $table->unique('mahasiswa_id', 'bimbingans_mahasiswa_id_unique');
        });
    }
};
