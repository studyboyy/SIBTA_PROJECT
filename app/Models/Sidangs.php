<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'mahasiswa_id',
    'sidang_batch_id',
    'jadwal',
    'jam_mulai',
    'jam_selesai',
    'ruangan',
    'gelombang',
    'ketua_sidang_id',
    'penguji_1_id',
    'penguji_2_id',
    'status',
    'file_kelengkapan',
])]
class Sidangs extends Model
{
    public function batch()
    {
        return $this->belongsTo(SidangBatch::class, 'sidang_batch_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function ketuaSidang()
    {
        return $this->belongsTo(Dosens::class, 'ketua_sidang_id');
    }

    public function penguji1()
    {
        return $this->belongsTo(Dosens::class, 'penguji_1_id');
    }

    public function penguji2()
    {
        return $this->belongsTo(Dosens::class, 'penguji_2_id');
    }
}
