<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganLog;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Support\MahasiswaProgressSummary;
use Carbon\CarbonInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Laporan extends Component
{
    use WithPagination;

    public int $detailPerPage = 10;

    public function updatedDetailPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->detailPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('progressPage');
    }

    #[Title('Laporan Admin')]
    public function render()
    {
        $mahasiswas = Mahasiswas::query()
            ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'sidang'])
            ->get();

        $progressRows = app(MahasiswaProgressSummary::class)->summarizeCollection($mahasiswas);
        $progressByMahasiswaId = $progressRows->keyBy('id');

        $semesterRows = $this->buildSemesterRecap($mahasiswas, $progressByMahasiswaId);
        $dosenWorkloads = $this->buildDosenWorkloads();
        $completionSummary = $this->buildCompletionSummary($progressRows);
        $progressRowsPaginated = $this->buildPaginatedProgressRows();

        return view('livewire.pages.laporan', [
            'semesterRows' => $semesterRows,
            'dosenWorkloads' => $dosenWorkloads,
            'completionSummary' => $completionSummary,
            'progressRows' => $progressRowsPaginated,
        ]);
    }

    private function buildPaginatedProgressRows(): LengthAwarePaginator
    {
        $mahasiswaPage = Mahasiswas::query()
            ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'sidang'])
            ->latest()
            ->paginate($this->detailPerPage, ['*'], 'progressPage');

        $summaries = app(MahasiswaProgressSummary::class)
            ->summarizeCollection(collect($mahasiswaPage->items()))
            ->values();

        $mahasiswaPage->setCollection($summaries);

        return $mahasiswaPage;
    }

    private function buildSemesterRecap(Collection $mahasiswas, Collection $progressByMahasiswaId): Collection
    {
        return $mahasiswas
            ->groupBy(fn(Mahasiswas $mahasiswa) => $this->academicSemesterLabel($mahasiswa->created_at))
            ->map(function (Collection $items, string $semester) use ($progressByMahasiswaId) {
                $rows = $items
                    ->map(fn(Mahasiswas $mahasiswa) => $progressByMahasiswaId->get($mahasiswa->id))
                    ->filter();

                $terjadwalSidang = $rows
                    ->filter(fn(array $row) => $row['has_sidang'] && $row['phase'] !== 'Selesai Sidang')
                    ->count();

                $selesaiSidang = $rows
                    ->filter(fn(array $row) => $row['phase'] === 'Selesai Sidang')
                    ->count();

                return [
                    'semester' => $semester,
                    'total_mahasiswa' => $rows->count(),
                    'siap_sidang' => $rows->where('is_ready_for_sidang', true)->count(),
                    'terjadwal_sidang' => $terjadwalSidang,
                    'selesai_sidang' => $selesaiSidang,
                    'rata_rata_progres' => (int) round($rows->avg('progress') ?? 0),
                ];
            })
            ->sortByDesc('semester')
            ->values();
    }

    private function buildDosenWorkloads(): Collection
    {
        $assignmentMap = Bimbingans::query()
            ->select('dosen_id', DB::raw('COUNT(*) as total_relasi'), DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'))
            ->groupBy('dosen_id')
            ->get()
            ->keyBy('dosen_id');

        $sessionMap = BimbinganLog::query()
            ->select(
                'dosen_id',
                DB::raw('COUNT(*) as total_sesi'),
                DB::raw("SUM(CASE WHEN konfirmasi_mahasiswa = 'hadir' THEN 1 ELSE 0 END) as total_hadir")
            )
            ->groupBy('dosen_id')
            ->get()
            ->keyBy('dosen_id');

        return Dosens::query()
            ->with('user')
            ->get()
            ->map(function (Dosens $dosen) use ($assignmentMap, $sessionMap) {
                $assignment = $assignmentMap->get($dosen->id);
                $session = $sessionMap->get($dosen->id);
                $kuota = max((int) ($dosen->kuota_bimbingan ?? 0), 0);
                $totalMahasiswa = (int) ($assignment->total_mahasiswa ?? 0);

                return [
                    'id' => $dosen->id,
                    'name' => $dosen->user?->name ?? 'Dosen',
                    'nidn' => $dosen->nidn,
                    'kuota' => $kuota,
                    'total_mahasiswa' => $totalMahasiswa,
                    'total_relasi' => (int) ($assignment->total_relasi ?? 0),
                    'total_sesi' => (int) ($session->total_sesi ?? 0),
                    'total_hadir' => (int) ($session->total_hadir ?? 0),
                    'persentase_beban' => $kuota > 0
                        ? (int) round(min(($totalMahasiswa / $kuota) * 100, 999))
                        : ($totalMahasiswa > 0 ? 100 : 0),
                ];
            })
            ->sortByDesc('total_mahasiswa')
            ->values();
    }

    private function buildCompletionSummary(Collection $progressRows): array
    {
        $total = $progressRows->count();

        $phaseDistribution = $progressRows
            ->groupBy('phase')
            ->map(fn(Collection $items, string $phase) => [
                'phase' => $phase,
                'count' => $items->count(),
                'percentage' => $total > 0 ? (int) round(($items->count() / $total) * 100) : 0,
            ])
            ->sortByDesc('count')
            ->values();

        return [
            'total' => $total,
            'rata_rata_progres' => (int) round($progressRows->avg('progress') ?? 0),
            'siap_sidang' => $progressRows->where('is_ready_for_sidang', true)->count(),
            'sudah_sidang' => $progressRows->where('phase', 'Selesai Sidang')->count(),
            'phase_distribution' => $phaseDistribution,
        ];
    }

    private function academicSemesterLabel(?CarbonInterface $date): string
    {
        if (! $date) {
            return 'Tidak diketahui';
        }

        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');

        if ($month >= 7) {
            return sprintf('Ganjil %d/%d', $year, $year + 1);
        }

        return sprintf('Genap %d/%d', $year - 1, $year);
    }
}
