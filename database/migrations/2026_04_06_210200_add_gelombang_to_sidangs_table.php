<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sidangs', function (Blueprint $table) {
            $table->unsignedInteger('gelombang')->nullable()->after('ruangan');
        });
    }

    public function down(): void
    {
        Schema::table('sidangs', function (Blueprint $table) {
            $table->dropColumn('gelombang');
        });
    }
};
