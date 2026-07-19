<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bimbingans')) {
            return;
        }

        // Older installations ran the single-supervisor migration and may
        // still have this index. It prevents pembimbing_2 from being saved.
        $indexes = Schema::getIndexes('bimbingans');
        $legacy = collect($indexes)->first(fn (array $index) =>
            ($index['name'] ?? '') === 'bimbingans_mahasiswa_id_unique'
            || (($index['unique'] ?? false) && ($index['columns'] ?? []) === ['mahasiswa_id'])
        );

        if ($legacy) {
            Schema::table('bimbingans', function (Blueprint $table) use ($legacy) {
                $table->dropIndex($legacy['name']);
            });
        }

        // Ensure the intended constraints exist even when an earlier migration
        // was interrupted after dropping the legacy index.
        $indexes = Schema::getIndexes('bimbingans');
        $names = collect($indexes)->pluck('name');
        Schema::table('bimbingans', function (Blueprint $table) use ($names) {
            if (! $names->contains('bimbingans_mahasiswa_dosen_unique')) {
                $table->unique(['mahasiswa_id', 'dosen_id'], 'bimbingans_mahasiswa_dosen_unique');
            }
            if (! $names->contains('bimbingans_mahasiswa_peran_unique')) {
                $table->unique(['mahasiswa_id', 'peran'], 'bimbingans_mahasiswa_peran_unique');
            }
        });
    }

    public function down(): void
    {
        // Keep the schema capable of storing two supervisors on rollback.
    }
};
