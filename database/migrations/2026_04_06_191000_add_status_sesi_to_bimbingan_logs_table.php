<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->string('status_sesi', 20)->default('diajukan')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bimbingan_logs', function (Blueprint $table) {
            $table->dropColumn('status_sesi');
        });
    }
};
