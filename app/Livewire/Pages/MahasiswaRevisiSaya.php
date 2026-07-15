<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesMahasiswaScope;
use App\Models\DokumenTa;
use App\Models\DokumenTaVersion;
use App\Models\SupervisorApproval;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MahasiswaRevisiSaya extends Component
{
    use UsesMahasiswaScope;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $status = 'perlu_revisi';

    public int $perPage = 10;

    public array $revisiFiles = [];

    protected string $paginationTheme = 'tailwind';

    #[Title('Revisi Saya')]
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

    public function kirimRevisi(int $dokumenId): void
    {
        $this->validate([
            'revisiFiles.'.$dokumenId => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $mahasiswa = $this->getMahasiswa();

        $dokumen = DokumenTa::query()
            ->whereKey($dokumenId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if (! in_array(strtolower((string) $dokumen->status), ['revisi', 'ditolak', 'rejected'], true)) {
            $this->addError('revisiFiles.'.$dokumenId, 'Dokumen ini tidak dalam status revisi.');

            return;
        }

        $file = $this->revisiFiles[$dokumenId] ?? null;
        $filename = now()->format('YmdHis')
            .'-'
            .Str::slug($mahasiswa->nim.'-revisi-'.$dokumen->bab)
            .'.'
            .$file->getClientOriginalExtension();

        $path = $file->storeAs('dokumen-ta/'.$mahasiswa->nim, $filename, 'public');

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
            'uploaded_by_user_id' => auth()->id(),
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
        $mahasiswa = $this->getMahasiswa();

        $dokumenList = DokumenTa::query()
            ->with(['versions.uploader'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($this->status === 'perlu_revisi', fn ($query) => $query->whereIn('status', ['revisi', 'ditolak', 'rejected']))
            ->when($this->status === 'menunggu', fn ($query) => $query->where('status', 'pending'))
            ->when($this->status === 'selesai', fn ($query) => $query->whereIn('status', ['approved', 'disetujui']))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('bab', 'like', "%{$search}%")
                        ->orWhere('jenis_dokumen', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN status IN ('revisi', 'ditolak', 'rejected') THEN 0 WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.mahasiswa-revisi-saya', [
            'mahasiswa' => $mahasiswa,
            'dokumenList' => $dokumenList,
            'summary' => [
                'perlu_revisi' => DokumenTa::query()->where('mahasiswa_id', $mahasiswa->id)->whereIn('status', ['revisi', 'ditolak', 'rejected'])->count(),
                'menunggu' => DokumenTa::query()->where('mahasiswa_id', $mahasiswa->id)->where('status', 'pending')->count(),
                'selesai' => DokumenTa::query()->where('mahasiswa_id', $mahasiswa->id)->whereIn('status', ['approved', 'disetujui'])->count(),
            ],
        ]);
    }
}
