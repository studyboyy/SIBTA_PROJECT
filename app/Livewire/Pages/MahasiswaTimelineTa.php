<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesMahasiswaScope;
use App\Models\BimbinganLog;
use App\Support\MahasiswaProgressSummary;
use App\Support\SidangDocumentCatalog;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaTimelineTa extends Component
{
    use UsesMahasiswaScope;

    #[Title('Timeline Progres TA')]
    public function render()
    {
        $mahasiswa = $this->getMahasiswa();

        $mahasiswa->load([
            'user',
            'programStudi',
            'pengajuanJuduls' => fn ($query) => $query->latest(),
            'bimbingans.dosen.user',
            'bimbinganLogs',
            'dokumenTa',
            'pengajuanSidang',
            'sidang.ketuaSidang.user',
            'sidang.penguji1.user',
            'sidang.penguji2.user',
        ]);

        $summary = app(MahasiswaProgressSummary::class)->summarize($mahasiswa);
        $checklist = SidangDocumentCatalog::checklist($mahasiswa->dokumenTa);
        $activeDosenIds = $this->getActiveDosenIds($mahasiswa->id);
        $bimbinganCount = BimbinganLog::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('dosen_id', $activeDosenIds)
            ->count();

        return view('livewire.pages.mahasiswa-timeline-ta', [
            'mahasiswa' => $mahasiswa,
            'summary' => $summary,
            'timelineSteps' => $this->buildTimelineSteps($mahasiswa, $checklist, $bimbinganCount),
            'latestJudul' => $mahasiswa->pengajuanJuduls->first(),
            'pembimbingAktif' => $mahasiswa->bimbingans->first(),
            'bimbinganCount' => $bimbinganCount,
            'checklist' => $checklist,
            'pengajuanSidang' => $mahasiswa->pengajuanSidang,
            'sidang' => $mahasiswa->sidang,
        ]);
    }

    private function buildTimelineSteps($mahasiswa, array $checklist, int $bimbinganCount): array
    {
        $latestJudul = $mahasiswa->pengajuanJuduls->first();
        $hasPembimbing = $mahasiswa->bimbingans->isNotEmpty();
        $hasProposal = (bool) ($checklist['proposal'] ?? false);
        $hasSkripsi = (bool) ($checklist['skripsi'] ?? false);
        $pengajuanSidang = $mahasiswa->pengajuanSidang;
        $sidang = $mahasiswa->sidang;

        return [
            [
                'label' => 'Pengajuan Judul',
                'description' => $latestJudul?->judul ?? 'Ajukan judul skripsi terlebih dahulu.',
                'status' => $this->stepStatus($latestJudul !== null, $latestJudul === null),
                'meta' => $latestJudul ? ucfirst((string) $latestJudul->status) : 'Belum diajukan',
            ],
            [
                'label' => 'Dosen Pembimbing',
                'description' => $hasPembimbing
                    ? ($mahasiswa->bimbingans->first()?->dosen?->user?->name ?? 'Pembimbing aktif')
                    : 'Menunggu penetapan dosen pembimbing.',
                'status' => $this->stepStatus($hasPembimbing, $latestJudul !== null && ! $hasPembimbing),
                'meta' => $hasPembimbing ? 'Aktif' : 'Belum ada',
            ],
            [
                'label' => 'Bimbingan',
                'description' => $bimbinganCount > 0
                    ? $bimbinganCount.' sesi bimbingan tercatat.'
                    : 'Ikuti jadwal bimbingan dari dosen pembimbing.',
                'status' => $this->stepStatus($bimbinganCount > 0, $hasPembimbing && $bimbinganCount === 0),
                'meta' => $bimbinganCount.' sesi',
            ],
            [
                'label' => 'Dokumen Proposal',
                'description' => 'Proposal harus berstatus disetujui oleh dosen pembimbing.',
                'status' => $this->stepStatus($hasProposal, $hasPembimbing && ! $hasProposal),
                'meta' => $hasProposal ? 'Disetujui' : 'Belum lengkap',
            ],
            [
                'label' => 'Dokumen Skripsi',
                'description' => 'Dokumen skripsi/laporan akhir harus disetujui.',
                'status' => $this->stepStatus($hasSkripsi, $hasProposal && ! $hasSkripsi),
                'meta' => $hasSkripsi ? 'Disetujui' : 'Belum lengkap',
            ],
            [
                'label' => 'Pengajuan Sidang',
                'description' => $pengajuanSidang
                    ? 'Pengajuan sidang sudah dikirim.'
                    : 'Kirim pengajuan sidang setelah dokumen wajib lengkap.',
                'status' => $this->stepStatus($pengajuanSidang !== null, $hasProposal && $hasSkripsi && $pengajuanSidang === null),
                'meta' => $pengajuanSidang ? ucfirst((string) $pengajuanSidang->status) : 'Belum diajukan',
            ],
            [
                'label' => 'ACC Kelayakan Dosen',
                'description' => $pengajuanSidang?->catatan_dosen ?: 'Dosen pembimbing meninjau kelayakan sidang.',
                'status' => $this->stepStatus($pengajuanSidang?->status_dosen === 'approved', $pengajuanSidang && $pengajuanSidang->status_dosen !== 'approved'),
                'meta' => $pengajuanSidang ? ucfirst((string) $pengajuanSidang->status_dosen) : 'Menunggu pengajuan',
            ],
            [
                'label' => 'Jadwal Sidang',
                'description' => $sidang
                    ? 'Sidang dijadwalkan pada '.Carbon::parse($sidang->jadwal)->translatedFormat('d M Y').'.'
                    : 'Jadwal dibuat admin setelah pengajuan disetujui.',
                'status' => $this->stepStatus($sidang !== null, $pengajuanSidang?->status_dosen === 'approved' && $sidang === null),
                'meta' => $sidang ? 'Terjadwal' : 'Belum terjadwal',
            ],
        ];
    }

    private function stepStatus(bool $done, bool $active): string
    {
        if ($done) {
            return 'done';
        }

        return $active ? 'active' : 'pending';
    }
}
