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
        DB::statement(
            'DELETE b1 FROM bimbingans b1 '
                . 'INNER JOIN bimbingans b2 '
                . 'ON b1.mahasiswa_id = b2.mahasiswa_id AND b1.id > b2.id'
        );

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
