<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\BimbinganLog;
use App\Models\Mahasiswas;
use App\Models\Pengajuanjuduls;
use App\Support\SidangDocumentCatalog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class DosenDashboard extends Component
{
    #[Title('Dashboard Dosen')]
    public function render()
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (! $dosen) {
            abort(404, 'Data dosen tidak ditemukan untuk akun ini.');
        }

        $mahasiswaIds = Bimbingans::query()
            ->where('dosen_id', $dosen->id)
            ->pluck('mahasiswa_id')
            ->values();

        $pengajuanQuery = Pengajuanjuduls::query()->whereIn('mahasiswa_id', $mahasiswaIds);

        $summary = [
            'totalMahasiswaBimbingan' => $mahasiswaIds->count(),
            'totalPengajuan' => (clone $pengajuanQuery)->count(),
            'pending' => (clone $pengajuanQuery)->where('status', 'pending')->count(),
            'approved' => (clone $pengajuanQuery)->whereIn('status', ['approved', 'disetujui'])->count(),
            'revisi' => (clone $pengajuanQuery)->where('status', 'revisi')->count(),
            'rejected' => (clone $pengajuanQuery)->whereIn('status', ['rejected', 'ditolak'])->count(),
        ];

        $bimbinganBaseQuery = BimbinganLog::query()->where('dosen_id', $dosen->id);
        $summary['bimbinganDiajukan'] = (clone $bimbinganBaseQuery)->where('status_sesi', 'diajukan')->count();
        $summary['bimbinganDisetujui'] = (clone $bimbinganBaseQuery)->where('status_sesi', 'disetujui')->count();
        $summary['bimbinganSelesai'] = (clone $bimbinganBaseQuery)->where('status_sesi', 'selesai')->count();
        $summary['bimbinganDibatalkan'] = (clone $bimbinganBaseQuery)->where('status_sesi', 'dibatalkan')->count();

        $totalSesi = (clone $bimbinganBaseQuery)->count();
        $totalHadir = (clone $bimbinganBaseQuery)->where('konfirmasi_mahasiswa', 'hadir')->count();
        $summary['progressBimbingan'] = $totalSesi > 0 ? (int) round(($totalHadir / $totalSesi) * 100) : 0;

        $latestPengajuan = Pengajuanjuduls::query()
            ->with(['mahasiswa.user'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->latest()
            ->take(6)
            ->get();

        $mahasiswaOverview = Mahasiswas::query()
            ->with([
                'user',
                'pengajuanJuduls' => fn($query) => $query->latest(),
            ])
            ->whereIn('id', $mahasiswaIds)
            ->get()
            ->map(function ($mahasiswa) {
                $latestTitle = $mahasiswa->pengajuanJuduls->first();

                return [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->user?->name ?? 'Mahasiswa',
                    'nim' => $mahasiswa->nim,
                    'latest_judul' => $latestTitle?->judul,
                    'latest_status' => $latestTitle?->status,
                    'latest_updated_at' => $latestTitle?->updated_at,
                    'total_pengajuan' => $mahasiswa->pengajuanJuduls->count(),
                ];
            })
            ->sortBy('name')
            ->values();

        $perluAksi = Pengajuanjuduls::query()
            ->with(['mahasiswa.user'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->whereIn('status', ['pending', 'revisi'])
            ->orderByRaw("case when status = 'pending' then 0 when status = 'revisi' then 1 else 2 end")
            ->orderBy('updated_at')
            ->take(6)
            ->get();

        $jadwalBimbingan = BimbinganLog::query()
            ->with(['mahasiswa.user'])
            ->where('dosen_id', $dosen->id)
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('id')
            ->take(6)
            ->get();

        $progressMahasiswa = Mahasiswas::query()
            ->with(['user', 'pengajuanSidang', 'dokumenTa'])
            ->withCount([
                'bimbinganLogs as hadir_bimbingan' => fn($q) => $q
                    ->where('konfirmasi_mahasiswa', 'hadir')
                    ->whereIn('status_sesi', ['disetujui', 'selesai']),
            ])
            ->whereIn('id', $mahasiswaIds)
            ->get()
            ->map(function ($mahasiswa) {
                $requiredChecklist = SidangDocumentCatalog::checklist($mahasiswa->dokumenTa);
                $requiredTotal = count(SidangDocumentCatalog::requiredTypes());
                $approvedRequired = count(array_filter($requiredChecklist));
                $isRequiredDocumentsComplete = $approvedRequired === $requiredTotal;
                $dokumenRatio = $requiredTotal > 0
                    ? ($approvedRequired / $requiredTotal)
                    : 0;

                $targetBimbingan = 8;
                $bimbinganRatio = min(($mahasiswa->hadir_bimbingan / $targetBimbingan), 1);

                $sidangStatus = strtolower((string) ($mahasiswa->pengajuanSidang?->status_dosen ?? $mahasiswa->pengajuanSidang?->status ?? 'pending'));
                $sidangScore = match ($sidangStatus) {
                    'approved' => 1,
                    'revisi' => 0.7,
                    'pending' => 0.4,
                    default => 0.2,
                };

                $progress = $isRequiredDocumentsComplete
                    ? 100
                    : (int) round(($dokumenRatio * 55) + ($bimbinganRatio * 35) + ($sidangScore * 10));

                $priority = match (true) {
                    $progress >= 80 => 'aman',
                    $progress >= 50 => 'perlu perhatian',
                    default => 'kritis',
                };

                return [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->user?->name ?? 'Mahasiswa',
                    'nim' => $mahasiswa->nim,
                    'prodi' => $mahasiswa->prodi,
                    'approved_dokumen' => $approvedRequired,
                    'total_dokumen' => $requiredTotal,
                    'hadir_bimbingan' => (int) $mahasiswa->hadir_bimbingan,
                    'target_bimbingan' => $targetBimbingan,
                    'sidang_status' => $sidangStatus,
                    'progress' => $progress,
                    'priority' => $priority,
                ];
            })
            ->sortByDesc('progress')
            ->values();

        return view('livewire.pages.dosen-dashboard', [
            'dosen' => $dosen,
            'summary' => $summary,
            'latestPengajuan' => $latestPengajuan,
            'mahasiswaOverview' => $mahasiswaOverview,
            'perluAksi' => $perluAksi,
            'jadwalBimbingan' => $jadwalBimbingan,
            'progressMahasiswa' => $progressMahasiswa,
        ]);
    }
}
