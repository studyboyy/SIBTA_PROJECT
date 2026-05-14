<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'dosen_id', 'tanggal', 'mode', 'jam', 'lokasi', 'link_online', 'catatan', 'catatan_mahasiswa', 'status', 'status_sesi', 'konfirmasi_mahasiswa', 'acc_at'])]
class BimbinganLog extends Model
{
    protected $casts = [
        'acc_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosens::class, 'dosen_id');
    }

    public function sessionAudits()
    {
        return $this->hasMany(BimbinganSessionAudit::class, 'bimbingan_log_id')->orderByDesc('changed_at')->orderByDesc('id');
    }

    public function bimbinganMessages()
    {
        // messages for the same mahasiswa; filtering by dosen is handled at query time
        return $this->hasMany(BimbinganMessage::class, 'mahasiswa_id', 'mahasiswa_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
