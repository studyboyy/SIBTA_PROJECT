<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'dosen_id', 'peran', 'tanggal', 'catatan', 'status'])]
class Bimbingans extends Model
{
    protected static function booted(): void
    {
        static::saved(fn (self $bimbingan) => $bimbingan->mahasiswa?->syncStatusTa());
        static::deleted(fn (self $bimbingan) => $bimbingan->mahasiswa?->syncStatusTa());
    }

    public static function setActiveSupervisor(int $mahasiswaId, int $dosenId): self
    {
        $assignment = static::query()
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('id')
            ->first();

        if ($assignment) {
            $assignment->update([
                'dosen_id' => $dosenId,
                'peran' => 'pembimbing',
                'tanggal' => $assignment->tanggal ?? now()->toDateString(),
                'status' => 'aktif',
            ]);

            static::query()
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('id', '!=', $assignment->id)
                ->delete();

            return $assignment->refresh();
        }

        return static::query()->create([
            'mahasiswa_id' => $mahasiswaId,
            'dosen_id' => $dosenId,
            'peran' => 'pembimbing',
            'tanggal' => now()->toDateString(),
            'status' => 'aktif',
        ]);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosens::class, 'dosen_id');
    }
}
