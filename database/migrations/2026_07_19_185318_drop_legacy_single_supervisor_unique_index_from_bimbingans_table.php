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
        if (! Schema::hasIndex('bimbingans', 'bimbingans_mahasiswa_id_unique')) {
            return;
        }

        // MySQL requires an index for mahasiswa_id while its foreign key exists.
        // The July migration already supplies two composite indexes whose first
        // column is mahasiswa_id, so the obsolete single-column unique index can
        // now be removed safely.
        Schema::table('bimbingans', function (Blueprint $table) {
            $table->dropUnique('bimbingans_mahasiswa_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally do not restore the legacy constraint: doing so would
        // reject valid pembimbing_2 rows and could fail when they already exist.
    }
};
