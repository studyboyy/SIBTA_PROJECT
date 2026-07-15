<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesDosenScope;
use App\Models\PengajuanSidang;
use App\Support\SidangDocumentCatalog;
use App\Support\SupervisorApprovalSync;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class DosenKelayakanSidang extends Component
{
    use UsesDosenScope;
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public int $perPage = 10;

    public array $catatanSidang = [];

    protected string $paginationTheme = 'tailwind';

    #[Title('Kelayakan Sidang')]
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function updateSidangStatus(int $pengajuanId, string $status): void
    {
        if (! in_array($status, ['approved', 'revisi', 'rejected'], true)) {
            return;
        }

        $pengajuan = $this->findOwnedPengajuan($pengajuanId);

        if ($status === 'approved') {
            $pengajuan->loadMissing('mahasiswa.dokumenTa');
            $checklist = SidangDocumentCatalog::checklist($pengajuan->mahasiswa?->dokumenTa ?? collect());

            if (count(array_filter($checklist)) < count(SidangDocumentCatalog::requiredTypes())) {
                session()->flash('error', 'Kelayakan sidang belum dapat di-ACC karena dokumen wajib belum lengkap.');

                return;
            }
        }

        if ($pengajuan->status_dosen === 'approved' && $pengajuan->status_kaprodi === 'approved') {
            session()->flash('error', 'Status kelayakan tidak dapat diubah setelah kaprodi menyetujui pengajuan sidang.');

            return;
        }

        $catatan = trim((string) ($this->catatanSidang[$pengajuanId] ?? ''));

        if (in_array($status, ['revisi', 'rejected'], true) && $catatan === '') {
            $this->addError('catatanSidang.'.$pengajuanId, 'Catatan wajib diisi untuk revisi/ditolak.');

            return;
        }

        $pengajuan->update([
            'catatan_dosen' => $catatan !== '' ? $catatan : null,
        ]);

        $approvalSync = app(SupervisorApprovalSync::class);
        $approvalSync->record($pengajuan, (int) $pengajuan->mahasiswa_id, (int) $this->getDosen()->id, $status, $catatan !== '' ? $catatan : null);
        $approvalSync->syncSidang($pengajuan->refresh());

        $this->catatanSidang[$pengajuanId] = '';

        session()->flash('success', 'Review kelayakan sidang berhasil disimpan. Status ACC aktif setelah semua pembimbing menyetujui.');
    }

    public function buildDigitalSignature(string $module, int $recordId, $approvedAt, ?int $signerUserId = null): ?string
    {
        if (! $approvedAt) {
            return null;
        }

        $timestamp = $approvedAt instanceof Carbon ? $approvedAt->timestamp : strtotime((string) $approvedAt);

        if (! $timestamp) {
            return null;
        }

        return strtoupper(substr(hash('sha256', implode('|', [
            $module,
            $recordId,
            $timestamp,
            (string) ($signerUserId ?? Auth::id() ?? 0),
        ])), 0, 16));
    }

    public function render()
    {
        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $pengajuanList = PengajuanSidang::query()
            ->with(['mahasiswa.user', 'mahasiswa.programStudi', 'mahasiswa.dokumenTa', 'mahasiswa.bimbinganLogs'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($this->status !== '', fn ($query) => $query->where('status_dosen', $this->status))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('catatan_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('catatan_dosen', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('mahasiswa', fn ($mahasiswaQuery) => $mahasiswaQuery->where('nim', 'like', "%{$search}%"));
                });
            })
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate($this->perPage);

        $checklists = $pengajuanList->getCollection()
            ->mapWithKeys(fn ($pengajuan) => [
                $pengajuan->id => SidangDocumentCatalog::checklist($pengajuan->mahasiswa?->dokumenTa ?? collect()),
            ]);

        return view('livewire.pages.dosen-kelayakan-sidang', [
            'dosen' => $dosen,
            'pengajuanList' => $pengajuanList,
            'checklists' => $checklists,
            'requiredTypes' => SidangDocumentCatalog::requiredTypes(),
            'documentLabels' => SidangDocumentCatalog::options(),
        ]);
    }

    private function findOwnedPengajuan(int $pengajuanId): PengajuanSidang
    {
        return PengajuanSidang::query()
            ->whereKey($pengajuanId)
            ->whereIn('mahasiswa_id', $this->getMahasiswaIdsForDosen())
            ->firstOrFail();
    }
}
