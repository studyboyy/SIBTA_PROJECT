<?php

namespace App\Support;

use App\Models\BimbinganLog;
use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\Pengajuanjuduls;
use App\Models\Sidangs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KaprodiReportSummary
{
    public function build(?int $year = null, string $semester = 'all', ?User $user = null): array
    {
        $mahasiswaQuery = Mahasiswas::query()
            ->with(['user', 'dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs', 'sidang']);

        if ($user && $user->hasRole('kaprodi') && ! $user->hasRole('pimpinan')) {
            $prodiId = $user->managedProdi?->id;
            $mahasiswaQuery->where('prodi_id', $prodiId ?? 0);
        }

        $this->applyPeriodFilter($mahasiswaQuery, $year, $semester);

        $mahasiswas = $mahasiswaQuery->get();
        $mahasiswaIds = $mahasiswas->pluck('id')->values();

        $progressRows = app(MahasiswaProgressSummary::class)->summarizeCollection($mahasiswas)->values();

        return [
            'statistik' => $this->buildStatistik($progressRows),
            'durasi' => $this->buildDurationSummary($mahasiswas, $mahasiswaIds),
            'beban_dosen' => $this->buildDosenWorkloads($mahasiswaIds),
            'phase_distribution' => $this->buildPhaseDistribution($progressRows),
            'progress_rows' => $progressRows,
            'available_years' => $this->buildAvailableYears($user),
        ];
    }

    private function buildStatistik(Collection $progressRows): array
    {
        $total = $progressRows->count();

        return [
            'total_mahasiswa_ta' => $total,
            'siap_sidang' => $progressRows->where('is_ready_for_sidang', true)->count(),
            'selesai_sidang' => $progressRows->where('phase', 'Selesai Sidang')->count(),
            'masih_proses' => $progressRows->whereNotIn('phase', ['Selesai Sidang'])->count(),
            'rata_rata_progres' => (int) round($progressRows->avg('progress') ?? 0),
        ];
    }

    private function buildDurationSummary(Collection $mahasiswas, Collection $mahasiswaIds): array
    {
        $judulMinMap = Pengajuanjuduls::query()
            ->select('mahasiswa_id', DB::raw('MIN(created_at) as first_judul_at'))
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->groupBy('mahasiswa_id')
            ->get()
            ->keyBy('mahasiswa_id');

        $sidangMap = Sidangs::query()
            ->select('mahasiswa_id', 'jadwal', 'status', 'created_at')
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->get()
            ->keyBy('mahasiswa_id');

        $completedDays = collect();
        $ongoingDays = collect();

        foreach ($mahasiswas as $mahasiswa) {
            $start = $judulMinMap->has($mahasiswa->id)
                ? Carbon::parse($judulMinMap[$mahasiswa->id]->first_judul_at)
                : Carbon::parse($mahasiswa->created_at);

            $sidang = $sidangMap->get($mahasiswa->id);

            if ($sidang && $this->isCompletedSidangStatus($sidang->status)) {
                $end = $sidang->jadwal ? Carbon::parse($sidang->jadwal) : Carbon::parse($sidang->created_at);
                $completedDays->push(max($start->diffInDays($end), 0));
                continue;
            }

            $ongoingDays->push(max($start->diffInDays(now()), 0));
        }

        return [
            'avg_hari_selesai' => (int) round($completedDays->avg() ?? 0),
            'min_hari_selesai' => $completedDays->count() > 0 ? (int) $completedDays->min() : 0,
            'max_hari_selesai' => $completedDays->count() > 0 ? (int) $completedDays->max() : 0,
            'total_mahasiswa_selesai' => $completedDays->count(),
            'avg_hari_berjalan' => (int) round($ongoingDays->avg() ?? 0),
        ];
    }

    private function buildDosenWorkloads(Collection $mahasiswaIds): Collection
    {
        if ($mahasiswaIds->isEmpty()) {
            return collect();
        }

        $assignmentMap = Bimbingans::query()
            ->select('dosen_id', DB::raw('COUNT(*) as total_relasi'), DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'))
            ->whereIn('mahasiswa_id', $mahasiswaIds)
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
            ->map(function (Dosens $dosen) use ($assignmentMap, $sessionMap) {
                $assignment = $assignmentMap->get($dosen->id);
                $session = $sessionMap->get($dosen->id);
                $kuota = max((int) ($dosen->kuota_bimbingan ?? 0), 0);
                $totalMahasiswa = (int) ($assignment->total_mahasiswa ?? 0);
                $utilisasi = $kuota > 0
                    ? (int) round(min(($totalMahasiswa / $kuota) * 100, 999))
                    : ($totalMahasiswa > 0 ? 100 : 0);

                return [
                    'id' => $dosen->id,
                    'name' => $dosen->user?->name ?? 'Dosen',
                    'nidn' => $dosen->nidn,
                    'kuota' => $kuota,
                    'total_mahasiswa' => $totalMahasiswa,
                    'total_relasi' => (int) ($assignment->total_relasi ?? 0),
                    'total_sesi' => (int) ($session->total_sesi ?? 0),
                    'total_hadir' => (int) ($session->total_hadir ?? 0),
                    'utilisasi' => $utilisasi,
                ];
            })
            ->sortByDesc('total_mahasiswa')
            ->values();
    }

    private function buildPhaseDistribution(Collection $progressRows): Collection
    {
        $total = $progressRows->count();

        return $progressRows
            ->groupBy('phase')
            ->map(fn(Collection $items, string $phase) => [
                'phase' => $phase,
                'count' => $items->count(),
                'percentage' => $total > 0 ? (int) round(($items->count() / $total) * 100) : 0,
            ])
            ->sortByDesc('count')
            ->values();
    }

    private function applyPeriodFilter(Builder $query, ?int $year, string $semester): void
    {
        if ($year !== null) {
            $query->whereYear('created_at', $year);
        }

        if ($semester === 'ganjil') {
            $query->whereMonth('created_at', '>=', 7);
        }

        if ($semester === 'genap') {
            $query->whereMonth('created_at', '<=', 6);
        }
    }

    private function buildAvailableYears(?User $user = null): Collection
    {
        return Mahasiswas::query()
            ->when($user && $user->hasRole('kaprodi') && ! $user->hasRole('pimpinan'), function ($query) use ($user) {
                $query->where('prodi_id', $user->managedProdi?->id ?? 0);
            })
            ->selectRaw('YEAR(created_at) as tahun')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn($year) => (int) $year)
            ->values();
    }

    private function isCompletedSidangStatus(?string $status): bool
    {
        $normalized = strtolower(trim((string) $status));

        return in_array($normalized, ['approved', 'disetujui', 'selesai', 'lulus'], true);
    }
}
