<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'mahasiswa_id',
    'dosen_id',
    'status',
    'alasan',
    'catatan_kaprodi',
    'diajukan_pada',
    'diproses_pada',
    'diproses_oleh',
])]
class PengajuanPembimbing extends Model
{
    protected $table = 'pengajuan_pembimbing';

    protected $casts = [
        'diajukan_pada' => 'datetime',
        'diproses_pada' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosens::class, 'dosen_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
