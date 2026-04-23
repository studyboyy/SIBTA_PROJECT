<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mahasiswa_id', 'dosen_id', 'sender_role', 'message', 'attachment', 'read_at'])]
class BimbinganMessage extends Model
{
    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswas::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosens::class, 'dosen_id');
    }
}
