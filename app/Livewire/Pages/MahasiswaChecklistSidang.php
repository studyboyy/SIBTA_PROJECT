<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesMahasiswaScope;
use App\Models\BimbinganLog;
use App\Support\SidangDocumentCatalog;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaChecklistSidang extends Component
{
    use UsesMahasiswaScope;

    #[Title('Checklist Kesiapan Sidang')]
    public function render()
    {
        $mahasiswa = $this->getMahasiswa();
        $mahasiswa->load(['dokumenTa', 'bimbingans.dosen.user', 'pengajuanSidang', 'sidang']);

        $activeDosenIds = $this->getActiveDosenIds($mahasiswa->id);
        $bimbinganCount = BimbinganLog::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('dosen_id', $activeDosenIds)
            ->count();

        $documentChecklist = SidangDocumentCatalog::checklist($mahasiswa->dokumenTa);
        $items = $this->buildChecklistItems($mahasiswa, $documentChecklist, $bimbinganCount);

        return view('livewire.pages.mahasiswa-checklist-sidang', [
            'mahasiswa' => $mahasiswa,
            'items' => $items,
            'readyCount' => collect($items)->where('done', true)->count(),
            'totalCount' => count($items),
            'isEligible' => ($documentChecklist['proposal'] ?? false)
                && ($documentChecklist['skripsi'] ?? false)
                && ! $mahasiswa->sidang,
        ]);
    }

    private function buildChecklistItems($mahasiswa, array $documentChecklist, int $bimbinganCount): array
    {
        $pengajuan = $mahasiswa->pengajuanSidang;

        return [
            [
                'label' => 'Dosen pembimbing aktif',
                'description' => $mahasiswa->bimbingans->first()?->dosen?->user?->name ?? 'Pembimbing belum tersedia.',
                'done' => $mahasiswa->bimbingans->isNotEmpty(),
                'action' => route('mahasiswa.pengajuan-judul'),
                'action_label' => 'Form Pengajuan',
            ],
            [
                'label' => 'Dokumen proposal disetujui',
                'description' => 'Proposal skripsi wajib berstatus disetujui.',
                'done' => (bool) ($documentChecklist['proposal'] ?? false),
                'action' => route('mahasiswa.dokumen'),
                'action_label' => 'Dokumen',
            ],
            [
                'label' => 'Dokumen skripsi disetujui',
                'description' => 'Laporan akhir/dokumen skripsi wajib berstatus disetujui.',
                'done' => (bool) ($documentChecklist['skripsi'] ?? false),
                'action' => route('mahasiswa.dokumen'),
                'action_label' => 'Dokumen',
            ],
            [
                'label' => 'Riwayat bimbingan tercatat',
                'description' => $bimbinganCount.' sesi bimbingan tercatat.',
                'done' => $bimbinganCount > 0,
                'action' => route('mahasiswa.bimbingan'),
                'action_label' => 'Bimbingan',
            ],
            [
                'label' => 'Pengajuan sidang terkirim',
                'description' => $pengajuan
                    ? 'Status dosen: '.ucfirst((string) $pengajuan->status_dosen).'.'
                    : 'Kirim pengajuan setelah dokumen wajib lengkap.',
                'done' => $pengajuan !== null,
                'action' => route('mahasiswa.pengajuan-sidang'),
                'action_label' => 'Pengajuan Sidang',
            ],
            [
                'label' => 'ACC kelayakan dari dosen',
                'description' => $pengajuan?->catatan_dosen ?: 'Menunggu tinjauan dosen pembimbing.',
                'done' => $pengajuan?->status_dosen === 'approved',
                'action' => route('mahasiswa.pengajuan-sidang'),
                'action_label' => 'Pengajuan Sidang',
            ],
            [
                'label' => 'Jadwal sidang ditetapkan',
                'description' => $mahasiswa->sidang
                    ? 'Jadwal: '.Carbon::parse($mahasiswa->sidang->jadwal)->translatedFormat('d M Y')
                    : 'Admin akan menetapkan jadwal setelah pengajuan disetujui.',
                'done' => $mahasiswa->sidang !== null,
                'action' => route('mahasiswa.jadwal-saya'),
                'action_label' => 'Jadwal Saya',
            ],
        ];
    }
}
