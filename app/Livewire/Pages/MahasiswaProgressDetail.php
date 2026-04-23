<?php

namespace App\Livewire\Pages;

use App\Models\Mahasiswas;
use App\Support\MahasiswaProgressSummary;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaProgressDetail extends Component
{
    public Mahasiswas $mahasiswa;

    #[Title('Detail Progres Mahasiswa')]
    public function mount(int $mahasiswaId): void
    {
        $this->mahasiswa = Mahasiswas::query()->findOrFail($mahasiswaId)->load([
            'user',
            'dokumenTa',
            'bimbingans.dosen.user',
            'bimbinganLogs.dosen.user',
            'sidang.ketuaSidang.user',
            'sidang.penguji1.user',
            'sidang.penguji2.user',
        ]);
    }

    public function render()
    {
        $summary = app(MahasiswaProgressSummary::class)->summarize($this->mahasiswa);

        $dokumenList = $this->mahasiswa->dokumenTa->sortByDesc('created_at')->values();
        $bimbinganList = $this->mahasiswa->bimbinganLogs->sortByDesc('tanggal')->values();

        return view('livewire.pages.mahasiswa-progress-detail', [
            'summary' => $summary,
            'dokumenList' => $dokumenList,
            'bimbinganList' => $bimbinganList,
            'sidang' => $this->mahasiswa->sidang,
        ]);
    }
}
