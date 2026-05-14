<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->text('catatan_mahasiswa')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->dropColumn('catatan_mahasiswa');
        });
    }
};
