<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesDosenScope;
use App\Models\User;
use App\Support\MahasiswaProgressSummary;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class DosenMonitoringMahasiswa extends Component
{
    use UsesDosenScope;
    use WithPagination;

    public string $search = '';

    public string $statusTa = '';

    public string $sidangStatus = '';

    public int $perPage = 10;

    protected string $paginationTheme = 'tailwind';

    #[Title('Monitoring Mahasiswa Bimbingan')]
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusTa(): void
    {
        $this->resetPage();
    }

    public function updatedSidangStatus(): void
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
        $dosen = $this->getDosen();

        $mahasiswaList = $this->mahasiswaBimbinganQuery($dosen)
            ->with([
                'user',
                'programStudi',
                'pengajuanJuduls' => fn ($query) => $query->latest(),
                'dokumenTa',
                'bimbingans.dosen.user',
                'bimbinganLogs' => fn ($query) => $query->where('dosen_id', $dosen->id)->latest('tanggal'),
                'pengajuanSidang',
                'sidang',
            ])
            ->when($this->statusTa !== '', fn ($query) => $query->where('status_ta', $this->statusTa))
            ->when($this->sidangStatus === 'belum_ajukan', fn ($query) => $query->doesntHave('pengajuanSidang'))
            ->when($this->sidangStatus !== '' && $this->sidangStatus !== 'belum_ajukan', fn ($query) => $query->whereHas('pengajuanSidang', fn ($sidangQuery) => $sidangQuery->where('status_dosen', $this->sidangStatus)))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nim', 'like', "%{$search}%")
                        ->orWhere('prodi', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('pengajuanJuduls', fn ($judulQuery) => $judulQuery->where('judul', 'like', "%{$search}%"));
                });
            })
            ->orderBy(User::query()
                ->select('name')
                ->whereColumn('users.id', 'mahasiswas.user_id')
                ->limit(1))
            ->paginate($this->perPage);

        $summaries = app(MahasiswaProgressSummary::class)
            ->summarizeCollection($mahasiswaList->getCollection())
            ->keyBy('id');

        return view('livewire.pages.dosen-monitoring-mahasiswa', [
            'dosen' => $dosen,
            'mahasiswaList' => $mahasiswaList,
            'summaries' => $summaries,
        ]);
    }
}
