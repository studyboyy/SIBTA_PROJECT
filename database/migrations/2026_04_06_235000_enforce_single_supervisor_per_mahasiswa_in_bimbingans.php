<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Keep the earliest assignment for each mahasiswa before adding unique constraint.
        $duplicateIds = DB::table('bimbingans as current')
            ->join('bimbingans as earlier', function ($join) {
                $join->on('current.mahasiswa_id', '=', 'earlier.mahasiswa_id')
                    ->on('current.id', '>', 'earlier.id');
            })
            ->pluck('current.id');

        if ($duplicateIds->isNotEmpty()) {
            DB::table('bimbingans')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('bimbingans', function (Blueprint $table) {
            $table->unique('mahasiswa_id', 'bimbingans_mahasiswa_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimbingans', function (Blueprint $table) {
            $table->dropUnique('bimbingans_mahasiswa_id_unique');
        });
    }
};
