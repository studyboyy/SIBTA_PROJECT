<?php

namespace App\Livewire\Pages;

use App\Models\DokumenTa;
use App\Models\PengajuanSidang;
use App\Models\Sidangs;
use App\Support\SidangDocumentCatalog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaPengajuanSidang extends Component
{
    #[Title('Pengajuan Sidang')]
    public string $catatan_mahasiswa = '';

    public function submit(): void
    {
        $this->validate([
            'catatan_mahasiswa' => ['nullable', 'string', 'max:3000'],
        ]);

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $checks = $this->buildChecklist($mahasiswa->id);

        if (collect($checks)->contains(false)) {
            $this->dispatch('notify', message: 'Pengajuan sidang belum dapat dikirim karena syarat belum lengkap.');
            return;
        }

        $pengajuan = PengajuanSidang::query()->firstOrNew(['mahasiswa_id' => $mahasiswa->id]);
        $pengajuan->status = 'pending';
        $pengajuan->status_dosen = 'pending';
        $pengajuan->status_kaprodi = 'pending';
        $pengajuan->gelombang = null;
        $pengajuan->catatan_mahasiswa = trim($this->catatan_mahasiswa) ?: null;
        $pengajuan->catatan_kaprodi = null;
        $pengajuan->diajukan_pada = now();
        $pengajuan->approved_kaprodi_at = null;
        $pengajuan->kaprodi_approved_by = null;
        $pengajuan->diproses_admin_pada = null;
        $pengajuan->save();

        $this->dispatch('notify', message: 'Pengajuan sidang berhasil dikirim.');
    }

    private function buildChecklist(int $mahasiswaId): array
    {
        $dokumen = DokumenTa::query()->where('mahasiswa_id', $mahasiswaId)->get();
        $dokumenChecklist = SidangDocumentCatalog::checklist($dokumen);
        $hasSidangSchedule = Sidangs::query()->where('mahasiswa_id', $mahasiswaId)->exists();

        return [
            'proposal' => $dokumenChecklist['proposal'],
            'laporan_ta' => $dokumenChecklist['laporan_ta'],
            'jurnal' => $dokumenChecklist['jurnal'],
            'bebas_lab' => $dokumenChecklist['bebas_lab'],
            'bebas_pustaka' => $dokumenChecklist['bebas_pustaka'],
            'belum_dijadwalkan' => ! $hasSidangSchedule,
        ];
    }

    public function render()
    {
        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $checklist = $this->buildChecklist($mahasiswa->id);
        $isEligible = ! collect($checklist)->contains(false);

        $pengajuan = PengajuanSidang::query()->where('mahasiswa_id', $mahasiswa->id)->first();

        if ($pengajuan && $this->catatan_mahasiswa === '' && $pengajuan->catatan_mahasiswa) {
            $this->catatan_mahasiswa = $pengajuan->catatan_mahasiswa;
        }

        return view('livewire.pages.mahasiswa-pengajuan-sidang', [
            'mahasiswa' => $mahasiswa,
            'checklist' => $checklist,
            'isEligible' => $isEligible,
            'pengajuan' => $pengajuan,
        ]);
    }
}
