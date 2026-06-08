<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'status', 'status_dosen', 'status_kaprodi', 'gelombang', 'catatan_mahasiswa', 'catatan_admin', 'catatan_dosen', 'catatan_kaprodi', 'diajukan_pada', 'acc_kelayakan_at', 'approved_kaprodi_at', 'diproses_admin_pada', 'kaprodi_approved_by'])]
class PengajuanSidang extends Model
{
    protected static function booted(): void
    {
        static::saved(fn (self $pengajuan) => $pengajuan->mahasiswa?->syncStatusTa());
        static::deleted(fn (self $pengajuan) => $pengajuan->mahasiswa?->syncStatusTa());
    }

    protected $casts = [
        'diajukan_pada' => 'datetime',
        'acc_kelayakan_at' => 'datetime',
        'approved_kaprodi_at' => 'datetime',
        'diproses_admin_pada' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function approverKaprodi()
    {
        return $this->belongsTo(User::class, 'kaprodi_approved_by');
    }
}
