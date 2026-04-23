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
        Schema::table('sidangs', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable()->after('jadwal');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            $table->foreignId('ketua_sidang_id')->nullable()->after('ruangan')->constrained('dosens')->nullOnDelete();
            $table->foreignId('penguji_1_id')->nullable()->after('ketua_sidang_id')->constrained('dosens')->nullOnDelete();
            $table->foreignId('penguji_2_id')->nullable()->after('penguji_1_id')->constrained('dosens')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sidangs', function (Blueprint $table) {
            $table->dropForeign(['ketua_sidang_id']);
            $table->dropForeign(['penguji_1_id']);
            $table->dropForeign(['penguji_2_id']);
            $table->dropColumn(['jam_mulai', 'jam_selesai', 'ketua_sidang_id', 'penguji_1_id', 'penguji_2_id']);
        });
    }
};
