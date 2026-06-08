<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganLog;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Prodi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

class KaprodiBebanDosen extends Component
{
    public string $search = '';

    public string $status = '';

    public string $prodiId = '';

    #[Title('Beban Dosen Pembimbing')]
    public function render()
    {
        $mahasiswaIds = $this->mahasiswaScope()->pluck('id');
        $workloads = $this->buildWorkloads($mahasiswaIds)
            ->when($this->search !== '', function (Collection $rows) {
                $search = str($this->search)->lower()->toString();

                return $rows->filter(function (array $row) use ($search) {
                    return str_contains(strtolower($row['name']), $search)
                        || str_contains(strtolower((string) $row['nidn']), $search);
                });
            })
            ->when($this->status !== '', fn (Collection $rows) => $rows->where('status_key', $this->status))
            ->values();

        $summary = [
            'total_dosen' => $workloads->count(),
            'penuh' => $workloads->where('status_key', 'penuh')->count(),
            'tinggi' => $workloads->where('status_key', 'tinggi')->count(),
            'longgar' => $workloads->where('status_key', 'longgar')->count(),
        ];

        return view('livewire.pages.kaprodi-beban-dosen', [
            'workloads' => $workloads,
            'summary' => $summary,
            'prodiOptions' => Prodi::query()->orderBy('name')->get(),
            'managedProdi' => Auth::user()?->managedProdi,
            'canSeeAllProdi' => $this->canSeeAllProdi(),
        ]);
    }

    private function mahasiswaScope()
    {
        return Mahasiswas::query()
            ->when(! $this->canSeeAllProdi(), fn ($query) => $query->where('prodi_id', $this->managedProdiId() ?? 0))
            ->when($this->canSeeAllProdi() && $this->prodiId !== '', fn ($query) => $query->where('prodi_id', (int) $this->prodiId));
    }

    private function buildWorkloads(Collection $mahasiswaIds): Collection
    {
        $assignmentMap = Bimbingans::query()
            ->select('dosen_id', DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'))
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->groupBy('dosen_id')
            ->get()
            ->keyBy('dosen_id');

        $totalAssignmentMap = Bimbingans::query()
            ->select('dosen_id', DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'))
            ->groupBy('dosen_id')
            ->get()
            ->keyBy('dosen_id');

        $sessionMap = BimbinganLog::query()
            ->select(
                'dosen_id',
                DB::raw('COUNT(*) as total_sesi'),
                DB::raw("SUM(CASE WHEN konfirmasi_mahasiswa = 'hadir' THEN 1 ELSE 0 END) as total_hadir")
            )
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->groupBy('dosen_id')
            ->get()
            ->keyBy('dosen_id');

        return Dosens::query()
            ->with('user')
            ->get()
            ->map(function (Dosens $dosen) use ($assignmentMap, $totalAssignmentMap, $sessionMap) {
                $assignment = $assignmentMap->get($dosen->id);
                $totalAssignment = $totalAssignmentMap->get($dosen->id);
                $session = $sessionMap->get($dosen->id);
                $kuota = max((int) ($dosen->kuota_bimbingan ?? 0), 0);
                $totalMahasiswaProdi = (int) ($assignment->total_mahasiswa ?? 0);
                $totalMahasiswaAktif = (int) ($totalAssignment->total_mahasiswa ?? 0);
                $sisa = max($kuota - $totalMahasiswaAktif, 0);
                $utilisasi = $kuota > 0 ? (int) round(($totalMahasiswaAktif / $kuota) * 100) : ($totalMahasiswaAktif > 0 ? 100 : 0);

                [$statusKey, $statusLabel] = match (true) {
                    $kuota > 0 && $totalMahasiswaAktif >= $kuota => ['penuh', 'Penuh'],
                    $utilisasi >= 80 => ['tinggi', 'Beban Tinggi'],
                    default => ['longgar', 'Masih Longgar'],
                };

                return [
                    'id' => $dosen->id,
                    'name' => $dosen->user?->name ?? 'Dosen',
                    'nidn' => $dosen->nidn,
                    'kuota' => $kuota,
                    'total_mahasiswa_prodi' => $totalMahasiswaProdi,
                    'total_mahasiswa_aktif' => $totalMahasiswaAktif,
                    'sisa' => $sisa,
                    'total_sesi' => (int) ($session->total_sesi ?? 0),
                    'total_hadir' => (int) ($session->total_hadir ?? 0),
                    'utilisasi' => min($utilisasi, 999),
                    'status_key' => $statusKey,
                    'status_label' => $statusLabel,
                    'status_rank' => match ($statusKey) {
                        'penuh' => 0,
                        'tinggi' => 1,
                        default => 2,
                    },
                ];
            })
            ->sortBy([
                ['status_rank', 'asc'],
                ['total_mahasiswa_aktif', 'desc'],
                ['name', 'asc'],
            ])
            ->values();
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
