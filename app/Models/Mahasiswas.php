<?php

namespace App\Models;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nim', 'angkatan', 'prodi', 'prodi_id', 'photo', 'status_ta', 'user_id'])]
class Mahasiswas extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dokumenTa()
    {
        return $this->hasMany(\App\Models\DokumenTa::class, 'mahasiswa_id');
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingans::class, 'mahasiswa_id');
    }

    public function bimbinganLogs()
    {
        return $this->hasMany(\App\Models\BimbinganLog::class, 'mahasiswa_id');
    }

    public function sidang()
    {
        return $this->hasOne(Sidangs::class, 'mahasiswa_id');
    }

    public function pengajuanJuduls()
    {
        return $this->hasMany(Pengajuanjuduls::class, 'mahasiswa_id');
    }

    public function bimbinganMessages()
    {
        return $this->hasMany(BimbinganMessage::class, 'mahasiswa_id');
    }

    public function pengajuanSidang()
    {
        return $this->hasOne(PengajuanSidang::class, 'mahasiswa_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
