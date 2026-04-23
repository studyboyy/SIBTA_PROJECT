<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->string('status_kaprodi')->default('pending')->after('status_dosen');
            $table->text('catatan_kaprodi')->nullable()->after('catatan_dosen');
            $table->timestamp('approved_kaprodi_at')->nullable()->after('acc_kelayakan_at');
            $table->foreignId('kaprodi_approved_by')->nullable()->after('approved_kaprodi_at')->constrained('users')->nullOnDelete();
        });

        DB::table('pengajuan_sidangs')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->get()
            ->each(function ($row) {
                DB::table('pengajuan_sidangs')
                    ->where('id', $row->id)
                    ->update([
                        'status_kaprodi' => $row->status === 'approved' ? 'approved' : 'pending',
                        'approved_kaprodi_at' => $row->status === 'approved' ? now() : null,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('pengajuan_sidangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kaprodi_approved_by');
            $table->dropColumn(['status_kaprodi', 'catatan_kaprodi', 'approved_kaprodi_at']);
        });
    }
};
