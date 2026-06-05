<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuanjuduls', function (Blueprint $table) {
            // Kolom calon dosen pembimbing yang diinginkan mahasiswa saat pengajuan judul
            $table->foreignId('calon_dosen_pembimbing_id')
                ->nullable()
                ->after('deskripsi')
                ->constrained('dosens')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuanjuduls', function (Blueprint $table) {
            $table->dropForeign(['calon_dosen_pembimbing_id']);
            $table->dropColumn('calon_dosen_pembimbing_id');
        });
    }
};
