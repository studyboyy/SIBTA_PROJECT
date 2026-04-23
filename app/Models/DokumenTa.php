<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'bab', 'jenis_dokumen', 'file', 'reviewer_markup_file', 'status', 'catatan', 'revisi_requested_at', 'revised_submitted_at'])]
class DokumenTa extends Model
{
    protected $table = 'dokumen_ta';

    protected $casts = [
        'revisi_requested_at' => 'datetime',
        'revised_submitted_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function versions()
    {
        return $this->hasMany(DokumenTaVersion::class, 'dokumen_ta_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
