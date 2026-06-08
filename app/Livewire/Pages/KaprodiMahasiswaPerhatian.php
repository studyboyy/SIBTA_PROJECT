<?php

namespace App\Livewire\Pages;

use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Support\MahasiswaProgressSummary;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class KaprodiMahasiswaPerhatian extends Component
{
    public string $search = '';

    public string $kategori = '';

    public string $prodiId = '';

    #[Title('Mahasiswa Perlu Perhatian')]
    public function render()
    {
        $rows = app(MahasiswaProgressSummary::class)
            ->summarizeCollection($this->mahasiswaScope()->get())
            ->filter(fn (array $row) => $row['urgency_score'] > 0)
            ->when($this->kategori !== '', fn ($rows) => $rows->filter(fn (array $row) => $this->matchesKategori($row)))
            ->when($this->search !== '', function ($rows) {
                $search = str($this->search)->lower()->toString();

                return $rows->filter(function (array $row) use ($search) {
                    return str_contains(strtolower($row['name']), $search)
                        || str_contains(strtolower($row['nim']), $search)
                        || str_contains(strtolower($row['prodi']), $search);
                });
            })
            ->sortByDesc('urgency_score')
            ->values();

        return view('livewire.pages.kaprodi-mahasiswa-perhatian', [
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'tanpa_pembimbing' => $rows->filter(fn (array $row) => ! $row['has_bimbingan'])->count(),
                'dokumen_ditolak' => $rows->filter(fn (array $row) => $row['rejected_documents'] > 0)->count(),
                'siap_belum_sidang' => $rows->filter(fn (array $row) => $row['is_ready_for_sidang'])->count(),
            ],
            'prodiOptions' => Prodi::query()->orderBy('name')->get(),
            'managedProdi' => Auth::user()?->managedProdi,
            'canSeeAllProdi' => $this->canSeeAllProdi(),
        ]);
    }

    private function mahasiswaScope()
    {
        return Mahasiswas::query()
            ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'sidang'])
            ->when(! $this->canSeeAllProdi(), fn ($query) => $query->where('prodi_id', $this->managedProdiId() ?? 0))
            ->when($this->canSeeAllProdi() && $this->prodiId !== '', fn ($query) => $query->where('prodi_id', (int) $this->prodiId));
    }

    private function matchesKategori(array $row): bool
    {
        return match ($this->kategori) {
            'tanpa_pembimbing' => ! $row['has_bimbingan'],
            'dokumen_ditolak' => $row['rejected_documents'] > 0,
            'dokumen_pending' => $row['pending_documents'] > 0,
            'siap_belum_sidang' => $row['is_ready_for_sidang'],
            default => true,
        };
    }

    private function canSeeAllProdi(): bool
    {
        $user = Auth::user();

        return (bool) $user?->hasRole('pimpinan');
    }

    private function managedProdiId(): ?int
    {
        $prodiId = Auth::user()?->managedProdi?->id;

        return $prodiId ? (int) $prodiId : null;
    }
}
