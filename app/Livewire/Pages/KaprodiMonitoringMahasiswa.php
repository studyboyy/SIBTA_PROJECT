<?php

namespace App\Livewire\Pages;

use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use App\Support\MahasiswaProgressSummary;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class KaprodiMonitoringMahasiswa extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusTa = '';

    public string $phase = '';

    public string $prodiId = '';

    public int $perPage = 10;

    protected string $paginationTheme = 'tailwind';

    #[Title('Monitoring Mahasiswa Prodi')]
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusTa(): void
    {
        $this->resetPage();
    }

    public function updatedPhase(): void
    {
        $this->resetPage();
    }

    public function updatedProdiId(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function render()
    {
        $query = Mahasiswas::query()
            ->with([
                'user',
                'programStudi.kaprodiUser',
                'bimbingans.dosen.user',
                'bimbinganLogs',
                'dokumenTa',
                'pengajuanSidang',
                'sidang.ketuaSidang.user',
                'sidang.penguji1.user',
                'sidang.penguji2.user',
            ])
            ->when(! $this->canSeeAllProdi(), function ($query) {
                $query->where('prodi_id', $this->managedProdiId() ?? 0);
            })
            ->when($this->canSeeAllProdi() && $this->prodiId !== '', function ($query) {
                $query->where('prodi_id', (int) $this->prodiId);
            })
            ->when($this->statusTa !== '', fn ($query) => $query->where('status_ta', $this->statusTa))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nim', 'like', "%{$search}%")
                        ->orWhere('prodi', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('bimbingans.dosen.user', fn ($dosenQuery) => $dosenQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('status_ta')
            ->orderBy(User::query()
                ->select('name')
                ->whereColumn('users.id', 'mahasiswas.user_id')
                ->limit(1))
            ->orderBy('nim');

        $allMahasiswa = $query->get();
        $summaries = app(MahasiswaProgressSummary::class)
            ->summarizeCollection($allMahasiswa)
            ->keyBy('id');

        if ($this->phase !== '') {
            $filteredIds = $summaries
                ->filter(fn (array $row) => $row['phase'] === $this->phase)
                ->keys()
                ->all();

            $allMahasiswa = $allMahasiswa->whereIn('id', $filteredIds)->values();
            $summaries = $summaries->only($filteredIds);
        }

        $page = $this->getPage();
        $mahasiswaList = new LengthAwarePaginator(
            $allMahasiswa->forPage($page, $this->perPage)->values(),
            $allMahasiswa->count(),
            $this->perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ],
        );

        return view('livewire.pages.kaprodi-monitoring-mahasiswa', [
            'mahasiswaList' => $mahasiswaList,
            'summaries' => $summaries,
            'phaseOptions' => $this->phaseOptions(),
            'prodiOptions' => $this->prodiOptions(),
            'managedProdi' => Auth::user()?->managedProdi,
            'canSeeAllProdi' => $this->canSeeAllProdi(),
        ]);
    }

    private function phaseOptions(): array
    {
        return [
            'Pending',
            'Proses Bimbingan',
            'Validasi Dokumen',
            'Siap Sidang',
            'Terjadwal Sidang',
            'Selesai Sidang',
        ];
    }

    private function prodiOptions()
    {
        return Prodi::query()->orderBy('name')->get();
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
