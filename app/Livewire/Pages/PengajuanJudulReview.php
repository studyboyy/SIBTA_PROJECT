<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Pengajuanjuduls;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class PengajuanJudulReview extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Review Pengajuan Judul')]
    public string $search = '';
    public string $status = '';
    public int $perPage = 10;
    public array $catatan = [];
    public array $editingStatusIds = [];

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

    public function mulaiEditStatus(int $pengajuanId): void
    {
        $this->assertPengajuanOwnedByAuthenticatedDosen($pengajuanId);

        if (! in_array($pengajuanId, $this->editingStatusIds, true)) {
            $this->editingStatusIds[] = $pengajuanId;
        }
    }

    public function batalEditStatus(int $pengajuanId): void
    {
        $this->editingStatusIds = array_values(array_filter(
            $this->editingStatusIds,
            fn($id) => (int) $id !== $pengajuanId
        ));

        unset($this->catatan[$pengajuanId]);
    }

    public function updateStatus(int $pengajuanId, string $status): void
    {
        if (! in_array($status, ['approved', 'revisi', 'rejected'], true)) {
            return;
        }

        $pengajuan = $this->assertPengajuanOwnedByAuthenticatedDosen($pengajuanId);
        $catatan = trim((string) ($this->catatan[$pengajuanId] ?? ''));

        if (in_array($status, ['revisi', 'rejected'], true) && $catatan === '') {
            $this->addError('catatan.' . $pengajuanId, 'Catatan wajib diisi untuk status revisi atau rejected.');
            return;
        }

        $pengajuan->update([
            'status' => $status,
            'catatan' => $catatan !== '' ? $catatan : null,
        ]);

        if (in_array($status, ['approved', 'rejected', 'revisi'], true)) {
            unset($this->catatan[$pengajuanId]);
        }

        $this->batalEditStatus($pengajuanId);
        session()->flash('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function render()
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (! $dosen) {
            abort(404, 'Data dosen tidak ditemukan untuk akun ini.');
        }

        $mahasiswaIds = $this->getPrimaryMahasiswaIdsForDosen($dosen->id);

        $pengajuanList = Pengajuanjuduls::query()
            ->with(['mahasiswa.user'])
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('judul', 'like', '%' . $this->search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                        ->orWhere('catatan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('mahasiswa', fn($q) => $q->where('nim', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.pengajuan-judul-review', [
            'dosen' => $dosen,
            'pengajuanList' => $pengajuanList,
        ]);
    }

    private function assertPengajuanOwnedByAuthenticatedDosen(int $pengajuanId): Pengajuanjuduls
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (! $dosen) {
            abort(403, 'Akun dosen tidak valid untuk melakukan review.');
        }

        $mahasiswaIds = $this->getPrimaryMahasiswaIdsForDosen($dosen->id);

        return Pengajuanjuduls::query()
            ->whereKey($pengajuanId)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->firstOrFail();
    }

    private function getPrimaryMahasiswaIdsForDosen(int $dosenId)
    {
        return Bimbingans::query()
            ->where('dosen_id', $dosenId)
            ->pluck('mahasiswa_id')
            ->values();
    }
}
