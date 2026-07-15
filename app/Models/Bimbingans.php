<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'dosen_id', 'peran', 'tanggal', 'catatan', 'status'])]
class Bimbingans extends Model
{
    public const PERAN_PEMBIMBING_1 = 'pembimbing_1';
    public const PERAN_PEMBIMBING_2 = 'pembimbing_2';

    protected static function booted(): void
    {
        static::saved(function (self $bimbingan) {
            $bimbingan->mahasiswa?->syncStatusTa();
            $bimbingan->syncSupervisorApprovalStates();
        });

        static::deleted(function (self $bimbingan) {
            $bimbingan->mahasiswa?->syncStatusTa();
            $bimbingan->syncSupervisorApprovalStates();
        });
    }

    public static function setActiveSupervisor(int $mahasiswaId, int $dosenId): self
    {
        return static::setSupervisor($mahasiswaId, $dosenId, self::PERAN_PEMBIMBING_1);
    }

    public static function setSupervisor(int $mahasiswaId, int $dosenId, string $peran): self
    {
        $assignment = static::query()
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('peran', $peran)
            ->orderBy('id')
            ->first();

        if ($assignment) {
            $assignment->update([
                'dosen_id' => $dosenId,
                'peran' => $peran,
                'tanggal' => $assignment->tanggal ?? now()->toDateString(),
                'status' => 'aktif',
            ]);

            return $assignment->refresh();
        }

        return static::query()->create([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'peran' => $peran,
            'tanggal' => now()->toDateString(),
            'status' => 'aktif',
        ]);
    }

    public static function peranOptions(): array
    {
        return [
            self::PERAN_PEMBIMBING_1 => 'Pembimbing 1',
            self::PERAN_PEMBIMBING_2 => 'Pembimbing 2',
        ];
    }

    public static function peranLabel(?string $peran): string
    {
        return self::peranOptions()[$peran] ?? 'Pembimbing';
    }

    public static function activeSupervisorIds(int $mahasiswaId)
    {
        return static::query()
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNotNull('dosen_id')
            ->orderByRaw("CASE peran WHEN 'pembimbing_1' THEN 1 WHEN 'pembimbing_2' THEN 2 ELSE 3 END")
            ->pluck('dosen_id')
            ->values();
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosens::class, 'dosen_id');
    }

    private function syncSupervisorApprovalStates(): void
    {
        $sync = app(\App\Support\SupervisorApprovalSync::class);
        $mahasiswa = $this->mahasiswa()->with(['pengajuanJuduls', 'dokumenTa', 'pengajuanSidang'])->first();

        if (! $mahasiswa) {
            return;
        }

        foreach ($mahasiswa->pengajuanJuduls as $pengajuan) {
            $sync->syncTitle($pengajuan);
        }

        foreach ($mahasiswa->dokumenTa as $dokumen) {
            $sync->syncDocument($dokumen);
        }

        if ($mahasiswa->pengajuanSidang) {
            $sync->syncSidang($mahasiswa->pengajuanSidang);
        }
    }
}
