<?php

namespace App\Livewire\Pages\Concerns;

use App\Models\Bimbingans;
use App\Models\Mahasiswas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait UsesMahasiswaScope
{
    protected function getMahasiswa(): Mahasiswas
    {
        $mahasiswa = Auth::user()?->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        return $mahasiswa;
    }

    protected function getActiveDosenIds(?int $mahasiswaId = null): Collection
    {
        return Bimbingans::query()
            ->where('mahasiswa_id', $mahasiswaId ?? $this->getMahasiswa()->id)
            ->pluck('dosen_id')
            ->filter()
            ->values();
    }
}
