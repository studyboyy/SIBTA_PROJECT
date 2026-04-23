<?php

namespace App\Livewire\Pages;

use App\Support\SidangDocumentCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaDashboard extends Component
{
    #[Title('Dashboard Mahasiswa')]
    public function render()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $mahasiswa->load(['dokumenTa', 'bimbingans.dosen.user', 'bimbinganLogs.dosen.user', 'sidang.ketuaSidang.user', 'sidang.penguji1.user', 'sidang.penguji2.user']);

        $hasPembimbing = $mahasiswa->bimbingans->contains(fn($item) => ! is_null($item->dosen_id));

        $primaryPembimbingName = $mahasiswa->bimbingans
            ->sortBy('id')
            ->first()?->dosen?->user?->name;

        $dokumenTotal = $mahasiswa->dokumenTa->count();
        $dokumenApproved = $mahasiswa->dokumenTa->whereIn('status', ['approved', 'disetujui'])->count();
        $dokumenPending = $mahasiswa->dokumenTa->whereIn('status', ['pending', 'menunggu'])->count();
        $requiredChecklist = SidangDocumentCatalog::checklist($mahasiswa->dokumenTa);
        $isRequiredDocumentsComplete = ! in_array(false, $requiredChecklist, true);

        $progress = $isRequiredDocumentsComplete
            ? 100
            : min(100, (int) round((($dokumenTotal > 0 ? ($dokumenApproved / $dokumenTotal) * 70 : 0) + ($mahasiswa->bimbinganLogs->count() > 0 ? 20 : 0) + ($mahasiswa->sidang ? 10 : 0))));

        $summary = [
            'dokumen_total' => $dokumenTotal,
            'dokumen_approved' => $dokumenApproved,
            'dokumen_pending' => $dokumenPending,
            'bimbingan_total' => $mahasiswa->bimbinganLogs->count(),
            'bimbingan_hadir' => $mahasiswa->bimbinganLogs->where('konfirmasi_mahasiswa', 'hadir')->count(),
            'sesi_selesai' => $mahasiswa->bimbinganLogs->where('status_sesi', 'selesai')->count(),
            'sesi_dibatalkan' => $mahasiswa->bimbinganLogs->where('status_sesi', 'dibatalkan')->count(),
            'progress' => $progress,
        ];

        $summary['progress_bimbingan'] = $summary['bimbingan_total'] > 0
            ? (int) round(($summary['bimbingan_hadir'] / $summary['bimbingan_total']) * 100)
            : 0;

        $upcomingKonsultasi = $mahasiswa->bimbinganLogs
            ->filter(fn($log) => $log->tanggal && Carbon::parse($log->tanggal)->startOfDay()->greaterThanOrEqualTo(now()->startOfDay()))
            ->sortBy('tanggal')
            ->take(3)
            ->values();

        return view('livewire.pages.mahasiswa-dashboard', [
            'mahasiswa' => $mahasiswa,
            'summary' => $summary,
            'hasPembimbing' => $hasPembimbing,
            'primaryPembimbingName' => $primaryPembimbingName,
            'latestBimbingan' => $mahasiswa->bimbinganLogs->sortByDesc('tanggal')->take(5),
            'upcomingKonsultasi' => $upcomingKonsultasi,
            'latestDokumen' => $mahasiswa->dokumenTa->sortByDesc('created_at')->take(5),
            'sidang' => $mahasiswa->sidang,
        ]);
    }
}
