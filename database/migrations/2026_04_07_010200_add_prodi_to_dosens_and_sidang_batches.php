<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sidang_batches', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('kuota')->constrained('prodis')->nullOnDelete();
        });

        $defaultProdiId = DB::table('prodis')->orderBy('id')->value('id');

        if ($defaultProdiId) {
            DB::table('sidang_batches')->whereNull('prodi_id')->update(['prodi_id' => $defaultProdiId]);
        }
    }

    public function down(): void
    {
        Schema::table('sidang_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prodi_id');
        });
    }
};
