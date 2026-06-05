<?php

namespace App\Livewire\Pages;

use App\Models\Dosens;
use App\Models\Pengajuanjuduls;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MahasiswaPengajuanJudul extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Pengajuan Judul Saya')]
    public string $judul = '';
    public string $deskripsi = '';
    public string $calon_dosen_pembimbing_id = '';
    public string $search = '';
    public string $status = '';
    public int $perPage = 10;
    public array $editingRevisionIds = [];
    public array $revisiJudul = [];
    public array $revisiDeskripsi = [];
    public array $revisiCatatan = [];

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

    public function save(): void
    {
        $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
            'calon_dosen_pembimbing_id' => ['nullable', 'exists:dosens,id'],
        ]);

        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $approvedTitle = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['approved', 'disetujui'])
            ->latest('updated_at')
            ->first();

        if ($approvedTitle) {
            $this->addError('judul', 'Judul Anda sudah disetujui dosen. Form pengajuan baru dinonaktifkan.');
            return;
        }

        Pengajuanjuduls::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => trim($this->judul),
            'deskripsi' => trim($this->deskripsi) ?: null,
            'calon_dosen_pembimbing_id' => $this->calon_dosen_pembimbing_id !== '' ? (int) $this->calon_dosen_pembimbing_id : null,
            'status' => 'pending',
        ]);

        $this->reset(['judul', 'deskripsi', 'calon_dosen_pembimbing_id']);
        $this->resetPage();
        $this->dispatch('notify', message: 'Pengajuan judul berhasil dikirim dan menunggu review.');
    }

    public function mulaiRevisi(int $pengajuanId): void
    {
        $pengajuan = $this->getOwnedPengajuan($pengajuanId);

        if (! in_array(strtolower((string) $pengajuan->status), ['revisi', 'rejected', 'ditolak'], true)) {
            return;
        }

        if (! in_array($pengajuanId, $this->editingRevisionIds, true)) {
            $this->editingRevisionIds[] = $pengajuanId;
        }

        $this->revisiJudul[$pengajuanId] = $pengajuan->judul;
        $this->revisiDeskripsi[$pengajuanId] = (string) ($pengajuan->deskripsi ?? '');
        $this->revisiCatatan[$pengajuanId] = '';
    }

    public function batalRevisi(int $pengajuanId): void
    {
        $this->editingRevisionIds = array_values(array_filter(
            $this->editingRevisionIds,
            fn($id) => (int) $id !== $pengajuanId
        ));

        unset($this->revisiJudul[$pengajuanId], $this->revisiDeskripsi[$pengajuanId], $this->revisiCatatan[$pengajuanId]);
    }

    public function kirimRevisi(int $pengajuanId): void
    {
        $this->validate([
            'revisiJudul.' . $pengajuanId => ['required', 'string', 'max:255'],
            'revisiDeskripsi.' . $pengajuanId => ['nullable', 'string', 'max:3000'],
            'revisiCatatan.' . $pengajuanId => ['nullable', 'string', 'max:1500'],
        ]);

        $pengajuan = $this->getOwnedPengajuan($pengajuanId);

        if (! in_array(strtolower((string) $pengajuan->status), ['revisi', 'rejected', 'ditolak'], true)) {
            $this->addError('revisiJudul.' . $pengajuanId, 'Judul ini tidak sedang dalam status revisi.');
            return;
        }

        $pengajuan->update([
            'judul' => trim((string) ($this->revisiJudul[$pengajuanId] ?? '')),
            'deskripsi' => trim((string) ($this->revisiDeskripsi[$pengajuanId] ?? '')) ?: null,
            'catatan_revisi_mahasiswa' => trim((string) ($this->revisiCatatan[$pengajuanId] ?? '')) ?: null,
            'revisi_ke' => ((int) $pengajuan->revisi_ke) + 1,
            'revisi_dikirim_pada' => now(),
            'status' => 'pending',
        ]);

        $this->batalRevisi($pengajuanId);
        session()->flash('success', 'Revisi judul berhasil dikirim dan menunggu review ulang dosen.');
    }

    public function render()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $primaryPembimbingName = $mahasiswa->bimbingans()
            ->with('dosen.user')
            ->orderBy('id')
            ->first()?->dosen?->user?->name;

        $pengajuanList = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                        ->orWhere('catatan', 'like', '%' . $this->search . '%');
                });
            })
            ->with('calonDosenPembimbing.user')
            ->latest()
            ->paginate($this->perPage);

        $approvedTitle = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['approved', 'disetujui'])
            ->latest('updated_at')
            ->first();

        $dosenOptions = Dosens::query()
            ->with('user')
            ->orderBy('nidn')
            ->get();

        return view('livewire.pages.mahasiswa-pengajuan-judul', [
            'mahasiswa' => $mahasiswa,
            'primaryPembimbingName' => $primaryPembimbingName,
            'approvedTitle' => $approvedTitle,
            'pengajuanList' => $pengajuanList,
            'dosenOptions' => $dosenOptions,
        ]);
    }

    private function getOwnedPengajuan(int $pengajuanId): Pengajuanjuduls
    {
        $mahasiswa = Auth::user()?->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        return Pengajuanjuduls::query()
            ->whereKey($pengajuanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();
    }
}
