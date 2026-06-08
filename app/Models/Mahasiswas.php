<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nim', 'angkatan', 'prodi', 'prodi_id', 'photo', 'status_ta', 'user_id'])]
class Mahasiswas extends Model
{
    use HasFactory;

    public const STATUS_TA_PENDING = 'Pending';

    public const STATUS_TA_PROSES = 'Proses';

    public const STATUS_TA_SELESAI = 'Selesai';

    protected static function booted(): void
    {
        static::saved(fn (self $mahasiswa) => $mahasiswa->syncStatusTa());
    }

    public function resolveStatusTa(): string
    {
        if ($this->hasCompletedSidang()) {
            return self::STATUS_TA_SELESAI;
        }

        if ($this->hasPembimbing()) {
            return self::STATUS_TA_PROSES;
        }

        return self::STATUS_TA_PENDING;
    }

    public function syncStatusTa(): void
    {
        $status = $this->resolveStatusTa();

        if ($this->status_ta === $status) {
            return;
        }

        $this->forceFill([
            'status_ta' => $status,
        ])->saveQuietly();
    }

    private function hasCompletedSidang(): bool
    {
        if ($this->relationLoaded('sidang')) {
            return $this->sidang !== null && Sidangs::isCompletedStatus($this->sidang->status);
        }

        return $this->sidang()
            ->whereIn('status', Sidangs::COMPLETED_STATUSES)
            ->exists();
    }

    private function hasPembimbing(): bool
    {
        if ($this->relationLoaded('bimbingans')) {
            return $this->bimbingans->isNotEmpty();
        }

        return $this->bimbingans()->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dokumenTa()
    {
        return $this->hasMany(DokumenTa::class, 'mahasiswa_id');
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingans::class, 'mahasiswa_id');
    }

    public function bimbinganLogs()
    {
        return $this->hasMany(BimbinganLog::class, 'mahasiswa_id');
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

    public function pengajuanPembimbing()
    {
        return $this->hasMany(PengajuanPembimbing::class, 'mahasiswa_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
