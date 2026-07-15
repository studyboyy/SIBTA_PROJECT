<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesDosenScope;
use App\Models\DokumenTa;
use App\Models\DokumenTaVersion;
use App\Support\SupervisorApprovalSync;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DosenReviewDokumen extends Component
{
    use UsesDosenScope;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public int $perPage = 10;

    public array $catatanDokumen = [];

    public array $reviewerMarkupFiles = [];

    protected string $paginationTheme = 'tailwind';

    #[Title('Review Dokumen TA')]
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

    public function setDokumenStatus(int $dokumenId, string $status): void
    {
        if (! in_array($status, ['disetujui', 'ditolak'], true)) {
            return;
        }

        $catatan = trim((string) ($this->catatanDokumen[$dokumenId] ?? ''));

        if ($status === 'ditolak' && $catatan === '') {
            $this->addError('catatanDokumen.'.$dokumenId, 'Catatan wajib diisi ketika dokumen ditolak.');

            return;
        }

        $dokumen = $this->findOwnedDokumen($dokumenId);

        $dokumen->update([
            'catatan' => $catatan !== '' ? $catatan : null,
        ]);

        $approvalSync = app(SupervisorApprovalSync::class);
        $approvalSync->record($dokumen, (int) $dokumen->mahasiswa_id, (int) $this->getDosen()->id, $status, $catatan !== '' ? $catatan : null);
        $approvalSync->syncDocument($dokumen->refresh());
        $dokumen->refresh();

        $this->createDokumenVersion($dokumen, 'status_update');
        $this->catatanDokumen[$dokumenId] = '';

        session()->flash('success', 'Review dokumen berhasil disimpan. Dokumen disetujui setelah semua pembimbing menyetujui.');
    }

    public function mintaRevisi(int $dokumenId): void
    {
        $this->validate([
            'catatanDokumen.'.$dokumenId => ['required', 'string', 'max:2000'],
            'reviewerMarkupFiles.'.$dokumenId => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:10240'],
        ]);

        $dosen = $this->getDosen();
        $dokumen = $this->findOwnedDokumen($dokumenId);
        $file = $this->reviewerMarkupFiles[$dokumenId] ?? null;
        $path = $dokumen->reviewer_markup_file;

        if ($file) {
            $filename = now()->format('YmdHis')
                .'-'
                .Str::slug('revisi-dosen-'.$dosen->id.'-dok-'.$dokumenId)
                .'.'
                .$file->getClientOriginalExtension();

            $path = $file->storeAs('dokumen-ta/reviewer-markup/dosen-'.$dosen->id, $filename, 'public');
        }

        $dokumen->update([
            'catatan' => trim((string) $this->catatanDokumen[$dokumenId]),
            'reviewer_markup_file' => $path,
            'revisi_requested_at' => now(),
        ]);

        $approvalSync = app(SupervisorApprovalSync::class);
        $approvalSync->record($dokumen, (int) $dokumen->mahasiswa_id, (int) $dosen->id, 'revisi', trim((string) $this->catatanDokumen[$dokumenId]));
        $approvalSync->syncDocument($dokumen->refresh());
        $dokumen->refresh();

        $this->createDokumenVersion($dokumen, 'review_revisi', $path);

        unset($this->reviewerMarkupFiles[$dokumenId]);
        $this->catatanDokumen[$dokumenId] = '';

        session()->flash('success', 'Permintaan revisi dokumen berhasil dikirim.');
    }

    public function render()
    {
        $dosen = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIdsForDosen($dosen->id);

        $dokumenList = DokumenTa::query()
            ->with(['mahasiswa.user', 'mahasiswa.programStudi', 'versions.uploader'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('bab', 'like', "%{$search}%")
                        ->orWhere('jenis_dokumen', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('mahasiswa', fn ($mahasiswaQuery) => $mahasiswaQuery->where('nim', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'revisi' THEN 1 WHEN status = 'ditolak' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.dosen-review-dokumen', [
            'dosen' => $dosen,
            'dokumenList' => $dokumenList,
        ]);
    }

    private function findOwnedDokumen(int $dokumenId): DokumenTa
    {
        return DokumenTa::query()
            ->whereKey($dokumenId)
            ->whereIn('mahasiswa_id', $this->getMahasiswaIdsForDosen())
            ->firstOrFail();
    }

    private function createDokumenVersion(DokumenTa $dokumen, string $action, ?string $file = null): void
    {
        DokumenTaVersion::query()->create([
            'dokumen_ta_id' => $dokumen->id,
            'uploaded_by_user_id' => Auth::id(),
            'uploader_role' => 'dosen',
            'action' => $action,
            'file' => $file,
            'note' => $dokumen->catatan,
            'status_snapshot' => $dokumen->status,
        ]);
    }
}
