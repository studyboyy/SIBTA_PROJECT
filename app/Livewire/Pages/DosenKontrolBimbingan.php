<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\DokumenTa;
use App\Models\DokumenTaVersion;
use App\Models\PengajuanSidang;
use App\Support\SidangDocumentCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class DosenKontrolBimbingan extends Component
{
    use WithFileUploads;
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Kontrol Bimbingan')]
    public string $search = '';
    public string $dokumenStatus = '';
    public string $timelineAction = '';
    public string $sidangStatus = '';
    public int $dokumenPerPage = 10;
    public int $sidangPerPage = 10;

    public array $catatanDokumen = [];
    public array $reviewerMarkupFiles = [];
    public array $catatanSidang = [];
    public array $editingDokumenIds = [];

    public function updatedSearch(): void
    {
        $this->resetPage('dokumenPage');
        $this->resetPage('bimbinganPage');
        $this->resetPage('sidangPage');
    }

    public function updatedDokumenStatus(): void
    {
        $this->resetPage('dokumenPage');
    }

    public function updatedSidangStatus(): void
    {
        $this->resetPage('sidangPage');
    }

    public function updatedTimelineAction(): void
    {
        $this->resetPage('dokumenPage');
    }

    public function updatedDokumenPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->dokumenPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('dokumenPage');
    }

    public function updatedSidangPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->sidangPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('sidangPage');
    }

    private function getDosen()
    {
        $dosen = Auth::user()?->dosen;

        if (! $dosen) {
            abort(403, 'Akun ini tidak terhubung ke data dosen.');
        }

        return $dosen;
    }

    private function getMahasiswaIdsForDosen(int $dosenId)
    {
        return Bimbingans::query()
            ->where('dosen_id', $dosenId)
            ->pluck('mahasiswa_id');
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

        $seed = implode('|', [
            $module,
            $recordId,
            $timestamp,
            (string) ($signerUserId ?? Auth::id() ?? 0),
        ]);

        return strtoupper(substr(hash('sha256', $seed), 0, 16));
    }

    public function signatureLabel(string $module): string
    {
        return match ($module) {
            'bimbingan' => 'SIG-BIM',
            'sidang' => 'SIG-SDG',
            default => 'SIG-SIBTA',
        };
    }

    public function updateDokumenStatus(int $dokumenId, string $status): void
    {
        if (! in_array($status, ['disetujui', 'ditolak'], true)) {
            return;
        }

        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $dokumen = DokumenTa::query()
            ->whereKey($dokumenId)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->firstOrFail();

        $dokumen->update([
            'status' => $status,
            'catatan' => trim((string) ($this->catatanDokumen[$dokumenId] ?? '')) ?: null,
        ]);

        DokumenTaVersion::query()->create([
            'dokumen_ta_id' => $dokumen->id,
            'uploaded_by_user_id' => Auth::id(),
            'uploader_role' => 'dosen',
            'action' => 'status_update',
            'note' => $dokumen->catatan,
            'status_snapshot' => $dokumen->status,
        ]);

        $this->editingDokumenIds[$dokumenId] = false;

        session()->flash('success', 'Status dokumen berhasil diperbarui.');
    }

    public function kirimRevisiDokumen(int $dokumenId): void
    {
        $this->validate([
            'catatanDokumen.' . $dokumenId => ['required', 'string', 'max:2000'],
            'reviewerMarkupFiles.' . $dokumenId => ['required', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:10240'],
        ]);

        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $dokumen = DokumenTa::query()
            ->whereKey($dokumenId)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->firstOrFail();

        $file = $this->reviewerMarkupFiles[$dokumenId] ?? null;
        if (! $file) {
            $this->addError('reviewerMarkupFiles.' . $dokumenId, 'File revisi bertanda wajib diunggah.');
            return;
        }

        $filename = now()->format('YmdHis')
            . '-' . Str::slug('revisi-dosen-' . $dosen->id . '-dok-' . $dokumenId)
            . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('dokumen-ta/reviewer-markup/dosen-' . $dosen->id, $filename, 'public');

        $dokumen->update([
            'status' => 'revisi',
            'catatan' => trim((string) ($this->catatanDokumen[$dokumenId] ?? '')),
            'reviewer_markup_file' => $path,
            'revisi_requested_at' => now(),
        ]);

        DokumenTaVersion::query()->create([
            'dokumen_ta_id' => $dokumen->id,
            'uploaded_by_user_id' => Auth::id(),
            'uploader_role' => 'dosen',
            'action' => 'review_revisi',
            'file' => $path,
            'note' => $dokumen->catatan,
            'status_snapshot' => $dokumen->status,
        ]);

        unset($this->reviewerMarkupFiles[$dokumenId]);
        $this->editingDokumenIds[$dokumenId] = false;
        session()->flash('success', 'File revisi bertanda berhasil dikirim ke mahasiswa.');
    }

    public function toggleEditDokumen(int $dokumenId): void
    {
        $current = (bool) ($this->editingDokumenIds[$dokumenId] ?? false);
        $this->editingDokumenIds[$dokumenId] = ! $current;
    }

    public function updateSidangStatus(int $pengajuanId, string $status): void
    {
        if (! in_array($status, ['approved', 'revisi', 'rejected'], true)) {
            return;
        }

        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $pengajuan = PengajuanSidang::query()
            ->with('mahasiswa.dokumenTa')
            ->whereKey($pengajuanId)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->firstOrFail();

        if ($status === 'approved') {
            $checklist = SidangDocumentCatalog::checklist($pengajuan->mahasiswa?->dokumenTa ?? collect());

            if (count(array_filter($checklist)) < count(SidangDocumentCatalog::requiredTypes())) {
                session()->flash('error', 'Kelayakan sidang belum dapat di-ACC karena dokumen wajib belum lengkap.');

                return;
            }
        }

        // Jika sudah approved dan sudah diproses kaprodi/admin, tidak boleh diubah
        if ($pengajuan->status_dosen === 'approved' && $pengajuan->status_kaprodi === 'approved') {
            session()->flash('error', 'Status kelayakan tidak dapat diubah setelah kaprodi menyetujui pengajuan sidang.');
            return;
        }

        $catatan = trim((string) ($this->catatanSidang[$pengajuanId] ?? ''));

        if (in_array($status, ['revisi', 'rejected'], true) && $catatan === '') {
            $this->addError('catatanSidang.' . $pengajuanId, 'Catatan wajib diisi untuk revisi/ditolak.');
            return;
        }

        $pengajuan->update([
            'status_dosen' => $status,
            'catatan_dosen' => $catatan !== '' ? $catatan : null,
            'acc_kelayakan_at' => $status === 'approved' ? now() : null,
        ]);

        session()->flash('success', 'Status kelayakan sidang berhasil diperbarui.');
    }

    public function render()
    {
        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $dokumenList = DokumenTa::query()
            ->with([
                'mahasiswa.user',
                'versions' => fn($query) => $query
                    ->when($this->timelineAction !== '', fn($q) => $q->where('action', $this->timelineAction))
                    ->with('uploader'),
            ])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($this->dokumenStatus !== '', fn($q) => $q->where('status', $this->dokumenStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('bab', 'like', '%' . $this->search . '%')
                        ->orWhere('catatan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.user', fn($qq) => $qq->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('mahasiswa', fn($qq) => $qq->where('nim', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest()
            ->paginate($this->dokumenPerPage, ['*'], 'dokumenPage');

        $pengajuanSidangList = PengajuanSidang::query()
            ->with(['mahasiswa.user'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($this->sidangStatus !== '', fn($q) => $q->where('status_dosen', $this->sidangStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('catatan_mahasiswa', 'like', '%' . $this->search . '%')
                        ->orWhere('catatan_dosen', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.user', fn($qq) => $qq->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('mahasiswa', fn($qq) => $qq->where('nim', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate($this->sidangPerPage, ['*'], 'sidangPage');

        return view('livewire.pages.dosen-kontrol-bimbingan', [
            'dosen' => $dosen,
            'dokumenList' => $dokumenList,
            'pengajuanSidangList' => $pengajuanSidangList,
        ]);
    }
}
