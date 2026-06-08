<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class KaprodiPengajuanJudul extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    #[Title('Pengajuan Dosen Pembimbing')]
    public string $searchPembimbing = '';

    public string $statusPembimbingFilter = '';

    public int $perPagePembimbing = 10;

    public ?int $actionPembimbingId = null;

    public string $actionType = 'approved';

    public string $catatanKaprodi = '';

    public function updatedSearchPembimbing(): void
    {
        $this->resetPage('pembimbingPage');
    }

    public function updatedStatusPembimbingFilter(): void
    {
        $this->resetPage('pembimbingPage');
    }

    public function updatedPerPagePembimbing($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPagePembimbing = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('pembimbingPage');
    }

    public function confirmAction(int $id, string $type): void
    {
        if (! in_array($type, ['approved', 'rejected'], true)) {
            return;
        }

        $this->actionPembimbingId = $id;
        $this->actionType = $type;
        $this->catatanKaprodi = '';
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'kaprodi-aksi-pembimbing');
    }

    public function prosesAksiPembimbing(): void
    {
        $this->validate([
            'catatanKaprodi' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        $prodiId = $this->getManagedProdiId();

        /** @var PengajuanPembimbing $pengajuan */
        $pengajuan = PengajuanPembimbing::query()
            ->whereKey($this->actionPembimbingId)
            ->whereHas('mahasiswa', fn ($q) => $q->where('prodi_id', $prodiId ?? 0))
            ->where('status', 'pending')
            ->firstOrFail();

        if ($this->actionType === 'approved') {
            $dosen = Dosens::withCount('bimbingans')->findOrFail($pengajuan->dosen_id);
            $pembimbingAktif = Bimbingans::query()
                ->where('mahasiswa_id', $pengajuan->mahasiswa_id)
                ->first();
            $kuotaTersisa = max((int) ($dosen->kuota_bimbingan ?? 0) - (int) ($dosen->bimbingans_count ?? 0), 0);
            $targetIsCurrentSupervisor = $pembimbingAktif && (int) $pembimbingAktif->dosen_id === (int) $pengajuan->dosen_id;

            if (! $targetIsCurrentSupervisor && $kuotaTersisa <= 0) {
                $this->addError('catatanKaprodi', 'Kuota dosen tujuan sudah penuh. Tidak bisa disetujui.');

                return;
            }

            DB::transaction(function () use ($pengajuan, $user) {
                Bimbingans::setActiveSupervisor((int) $pengajuan->mahasiswa_id, (int) $pengajuan->dosen_id);

                $pengajuan->update([
                    'status' => 'approved',
                    'catatan_kaprodi' => trim($this->catatanKaprodi) ?: null,
                    'diproses_pada' => now(),
                    'diproses_oleh' => $user->id,
                ]);
            });

            $this->dispatch('close-modal', name: 'kaprodi-aksi-pembimbing');
            $this->dispatch('notify', message: 'Pengajuan pembimbing disetujui. Dosen pembimbing mahasiswa telah diganti.');
        } else {
            $pengajuan->update([
                'status' => 'rejected',
                'catatan_kaprodi' => trim($this->catatanKaprodi) ?: null,
                'diproses_pada' => now(),
                'diproses_oleh' => $user->id,
            ]);

            $this->dispatch('close-modal', name: 'kaprodi-aksi-pembimbing');
            $this->dispatch('notify', message: 'Pengajuan pembimbing ditolak.');
        }

        $this->reset(['actionPembimbingId', 'actionType', 'catatanKaprodi']);
    }

    public function render()
    {
        $user = Auth::user();
        $prodiId = $this->getManagedProdiId();

        $pengajuanPembimbingList = PengajuanPembimbing::query()
            ->with(['mahasiswa.user', 'mahasiswa.bimbingans.dosen.user', 'dosen.user'])
            ->whereHas('mahasiswa', fn ($q) => $q->where('prodi_id', $prodiId ?? 0))
            ->when($this->statusPembimbingFilter !== '', fn ($q) => $q->where('status', $this->statusPembimbingFilter))
            ->when($this->searchPembimbing !== '', function ($q) {
                $search = trim($this->searchPembimbing);
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('mahasiswa.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('mahasiswa', fn ($mahasiswaQuery) => $mahasiswaQuery->where('nim', 'like', "%{$search}%"))
                        ->orWhereHas('dosen.user', fn ($dosenQuery) => $dosenQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('diajukan_pada')
            ->paginate($this->perPagePembimbing, ['*'], 'pembimbingPage');

        return view('livewire.pages.kaprodi-pengajuan-judul', [
            'pengajuanPembimbingList' => $pengajuanPembimbingList,
            'managedProdi' => $user?->managedProdi,
        ]);
    }

    private function getManagedProdiId(): ?int
    {
        $prodiId = Auth::user()?->managedProdi?->id;

        return $prodiId ? (int) $prodiId : null;
    }
}
