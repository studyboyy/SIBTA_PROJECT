<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->string('mode', 20)->default('offline')->after('tanggal');
            $table->time('jam')->nullable()->after('mode');
            $table->string('lokasi')->nullable()->after('jam');
            $table->string('link_online')->nullable()->after('lokasi');
            $table->string('konfirmasi_mahasiswa', 20)->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->dropColumn(['mode', 'jam', 'lokasi', 'link_online', 'konfirmasi_mahasiswa']);
        });
    }
};
