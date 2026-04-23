<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tanggal',
    'jam_mulai',
    'jam_selesai',
    'ruangan',
    'gelombang',
    'kuota',
    'prodi_id',
    'ketua_sidang_id',
    'penguji_1_id',
    'penguji_2_id',
])]
class SidangBatch extends Model
{
    public function programStudi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
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

    public function sidangs()
    {
        return $this->hasMany(Sidangs::class, 'sidang_batch_id');
    }
}
