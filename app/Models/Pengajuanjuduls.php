<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dosens;

#[Fillable(['mahasiswa_id', 'judul', 'deskripsi', 'calon_dosen_pembimbing_id', 'status', 'catatan', 'revisi_ke', 'catatan_revisi_mahasiswa', 'revisi_dikirim_pada'])]
class Pengajuanjuduls extends Model
{
    protected $casts = [
        'revisi_dikirim_pada' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function calonDosenPembimbing()
    {
        return $this->belongsTo(Dosens::class, 'calon_dosen_pembimbing_id');
    }
}
