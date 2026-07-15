<?php

namespace App\Livewire\Pages;

use App\Models\DokumenTa;
use App\Models\DokumenTaVersion;
use App\Models\SupervisorApproval;
use App\Support\SidangDocumentCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MahasiswaDokumen extends Component
{
    use WithFileUploads;
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Dokumen Saya')]
    public string $documentType = '';
    public string $bab = '';
    public string $timelineAction = '';
    public int $perPage = 10;
    public $file;
    public array $revisiFiles = [];

    public function updatedTimelineAction(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'documentType' => ['required', 'string', 'max:100'],
            'bab' => ['nullable', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $documentTitle = $this->documentType === 'lainnya'
            ? trim($this->bab)
            : SidangDocumentCatalog::label($this->documentType);

        if ($documentTitle === '') {
            $this->addError('bab', 'Nama dokumen wajib diisi untuk dokumen lainnya.');
            return;
        }

        $filename = now()->format('YmdHis')
            . '-' . Str::slug($mahasiswa->nim . '-' . $documentTitle)
            . '.' . $this->file->getClientOriginalExtension();

        $path = $this->file->storeAs('dokumen-ta/' . $mahasiswa->nim, $filename, 'public');

        $dokumen = DokumenTa::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when(
                $this->documentType === 'lainnya',
                fn($query) => $query->where('bab', $documentTitle),
                fn($query) => $query->where('jenis_dokumen', $this->documentType)
            )
            ->first();

        if ($dokumen) {
            $dokumen->update([
                'bab' => $documentTitle,
                'jenis_dokumen' => $this->documentType,
                'file' => $path,
                'status' => 'pending',
                'catatan' => null,
            ]);

            SupervisorApproval::query()
                ->where('approvable_type', DokumenTa::class)
                ->where('approvable_id', $dokumen->id)
                ->delete();
        } else {
            $dokumen = DokumenTa::query()->create([
                'mahasiswa_id' => $mahasiswa->id,
                'bab' => $documentTitle,
                'jenis_dokumen' => $this->documentType,
                'file' => $path,
                'status' => 'pending',
            ]);
        }

        DokumenTaVersion::query()->create([
            'dokumen_ta_id' => $dokumen->id,
            'uploaded_by_user_id' => $user->id,
            'uploader_role' => 'mahasiswa',
            'action' => 'upload',
            'file' => $path,
            'status_snapshot' => 'pending',
        ]);

        $this->reset(['documentType', 'bab', 'file']);
        $this->resetPage();
        $this->dispatch('notify', message: 'Dokumen berhasil diunggah, menunggu verifikasi dosen');
    }

    public function kirimRevisi(int $dokumenId): void
    {
        $this->validate([
            'revisiFiles.' . $dokumenId => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $dokumen = DokumenTa::query()
            ->whereKey($dokumenId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if (! in_array(strtolower((string) $dokumen->status), ['revisi', 'ditolak', 'rejected'], true)) {
            $this->addError('revisiFiles.' . $dokumenId, 'Dokumen ini tidak dalam status revisi.');
            return;
        }

        $file = $this->revisiFiles[$dokumenId] ?? null;
        if (! $file) {
            $this->addError('revisiFiles.' . $dokumenId, 'File hasil revisi wajib diunggah.');
            return;
        }

        $filename = now()->format('YmdHis')
            . '-' . Str::slug($mahasiswa->nim . '-revisi-' . $dokumen->bab)
            . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('dokumen-ta/' . $mahasiswa->nim, $filename, 'public');

        $dokumen->update([
            'file' => $path,
            'status' => 'pending',
            'revised_submitted_at' => now(),
        ]);

        SupervisorApproval::query()
            ->where('approvable_type', DokumenTa::class)
            ->where('approvable_id', $dokumen->id)
            ->delete();

        DokumenTaVersion::query()->create([
            'dokumen_ta_id' => $dokumen->id,
            'uploaded_by_user_id' => $user->id,
            'uploader_role' => 'mahasiswa',
            'action' => 'resubmission',
            'file' => $path,
            'status_snapshot' => 'pending',
        ]);

        unset($this->revisiFiles[$dokumenId]);
        session()->flash('success', 'Dokumen hasil revisi berhasil dikirim ke dosen.');
    }

    public function render()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $dokumenList = DokumenTa::query()
            ->with([
                'versions' => fn($query) => $query
                    ->when($this->timelineAction !== '', fn($q) => $q->where('action', $this->timelineAction))
                    ->with('uploader'),
            ])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->paginate($this->perPage);

        $kelayakanChecklist = SidangDocumentCatalog::checklist(
            DokumenTa::query()->where('mahasiswa_id', $mahasiswa->id)->get()
        );

        return view('livewire.pages.mahasiswa-dokumen', [
            'mahasiswa' => $mahasiswa,
            'dokumenList' => $dokumenList,
            'documentOptions' => SidangDocumentCatalog::options(),
            'requiredDocumentLabels' => [
                'proposal' => SidangDocumentCatalog::label('proposal'),
                'skripsi'  => SidangDocumentCatalog::label('skripsi'),
            ],
            'kelayakanChecklist' => $kelayakanChecklist,
        ]);
    }
}
