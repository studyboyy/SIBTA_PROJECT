<?php

namespace App\Livewire\Pages\Concerns;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait UsesDosenScope
{
    protected function getDosen(): Dosens
    {
        $dosen = Auth::user()?->dosen;

        if (! $dosen) {
            abort(403, 'Akun ini tidak terhubung ke data dosen.');
        }

        return $dosen;
    }

    protected function getMahasiswaIdsForDosen(?int $dosenId = null): Collection
    {
        return Bimbingans::query()
            ->where('dosen_id', $dosenId ?? $this->getDosen()->id)
            ->pluck('mahasiswa_id');
    }

    protected function mahasiswaBimbinganQuery(?Dosens $dosen = null): Builder
    {
        $dosen ??= $this->getDosen();

        return Mahasiswas::query()
            ->whereIn('id', $this->getMahasiswaIdsForDosen($dosen->id));
    }
}
