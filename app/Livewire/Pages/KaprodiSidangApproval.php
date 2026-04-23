<?php

namespace App\Livewire\Pages;

use App\Models\PengajuanSidang;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class KaprodiSidangApproval extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public int $perPage = 10;
    public ?int $actionId = null;
    public string $actionType = 'approved';
    public string $catatan_kaprodi = '';

    #[Title('Approval Sidang Kaprodi')]
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function confirmAction(int $id, string $type): void
    {
        $this->actionId = $id;
        $this->actionType = $type;
        $this->catatan_kaprodi = '';
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'kaprodi-sidang-action');
    }

    public function submitAction(): void
    {
        $this->validate([
            'catatan_kaprodi' => ['nullable', 'string', 'max:3000'],
        ]);

        $user = Auth::user();
        $prodiId = $user?->managedProdi?->id;

        $pengajuan = PengajuanSidang::query()
            ->whereKey($this->actionId)
            ->whereHas('mahasiswa', fn($query) => $query->where('prodi_id', $prodiId ?? 0))
            ->firstOrFail();

        $status = $this->actionType === 'rejected' ? 'rejected' : 'approved';

        $pengajuan->update([
            'status_kaprodi' => $status,
            'catatan_kaprodi' => trim($this->catatan_kaprodi) ?: null,
            'approved_kaprodi_at' => $status === 'approved' ? now() : null,
            'kaprodi_approved_by' => $status === 'approved' ? $user?->id : null,
            'status' => $status === 'rejected' ? 'rejected' : $pengajuan->status,
            'diproses_admin_pada' => $status === 'rejected' ? now() : $pengajuan->diproses_admin_pada,
        ]);

        $this->dispatch('close-modal', name: 'kaprodi-sidang-action');
        $this->dispatch('notify', message: $status === 'approved' ? 'Pengajuan sidang disetujui kaprodi.' : 'Pengajuan sidang ditolak kaprodi.');
        $this->reset(['actionId', 'actionType', 'catatan_kaprodi']);
    }

    public function render()
    {
        $user = Auth::user();
        $prodiId = $user?->managedProdi?->id;

        $pengajuanList = PengajuanSidang::query()
            ->with(['mahasiswa.user', 'mahasiswa.programStudi'])
            ->whereHas('mahasiswa', fn($query) => $query->where('prodi_id', $prodiId ?? 0))
            ->when($this->statusFilter !== '', fn($query) => $query->where('status_kaprodi', $this->statusFilter))
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('mahasiswa.user', fn($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('mahasiswa', fn($mahasiswaQuery) => $mahasiswaQuery->where('nim', 'like', '%' . $search . '%'));
                });
            })
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.pages.kaprodi-sidang-approval', [
            'pengajuanList' => $pengajuanList,
            'managedProdi' => $user?->managedProdi,
        ]);
    }
}
