<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Pengajuanjuduls;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

/**
 * Halaman Kaprodi untuk melihat pengajuan judul mahasiswa di prodinya
 * beserta calon dosen pembimbing yang diinginkan mahasiswa.
 * Kaprodi dapat menyetujui (dosen tetap sesuai pilihan mahasiswa)
 * atau mengganti dosen pembimbing.
 */
class KaprodiPengajuanJudul extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Pengajuan Judul & Pembimbing')]

    public string $search = '';
    public string $statusFilter = '';
    public int $perPage = 10;

    // Modal konfirmasi penetapan dosen
    public ?int $actionPengajuanId = null;
    public string $dosenTerpilihId = '';
    public string $catatanKaprodi = '';

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

    public function bukaModalTetapkan(int $pengajuanId): void
    {
        $pengajuan = $this->getPengajuanMilikProdi($pengajuanId);
        $this->actionPengajuanId = $pengajuan->id;
        // Pre-fill dengan calon dosen pilihan mahasiswa jika ada
        $this->dosenTerpilihId = (string) ($pengajuan->calon_dosen_pembimbing_id ?? '');
        $this->catatanKaprodi = '';
        $this->resetErrorBag();
        $this->dispatch('open-modal', name: 'kaprodi-tetapkan-dosen');
    }

    public function tetapkanDosen(): void
    {
        $this->validate([
            'dosenTerpilihId' => ['required', 'exists:dosens,id'],
        ]);

        $pengajuan = $this->getPengajuanMilikProdi((int) $this->actionPengajuanId);
        $mahasiswaId = $pengajuan->mahasiswa_id;
        $dosenId = (int) $this->dosenTerpilihId;

        // Cek kuota dosen
        $dosen = Dosens::withCount('bimbingans')->findOrFail($dosenId);
        $sudahPunyaPembimbing = Bimbingans::where('mahasiswa_id', $mahasiswaId)->exists();
        $kuotaTersisa = max((int) ($dosen->kuota_bimbingan ?? 0) - (int) ($dosen->bimbingans_count ?? 0), 0);

        // Jika mahasiswa belum punya pembimbing (penugasan baru), cek kuota
        if (! $sudahPunyaPembimbing && $kuotaTersisa <= 0) {
            $this->addError('dosenTerpilihId', 'Kuota dosen ini sudah penuh. Pilih dosen lain.');
            return;
        }

        DB::transaction(function () use ($mahasiswaId, $dosenId) {
            // Hapus assignment pembimbing lama jika ada
            Bimbingans::where('mahasiswa_id', $mahasiswaId)->delete();

            // Assign dosen pembimbing baru
            Bimbingans::create([
                'mahasiswa_id' => $mahasiswaId,
                'dosen_id'     => $dosenId,
            ]);
        });

        $this->dispatch('close-modal', name: 'kaprodi-tetapkan-dosen');
        $this->dispatch('notify', message: 'Dosen pembimbing berhasil ditetapkan.');
        $this->reset(['actionPengajuanId', 'dosenTerpilihId', 'catatanKaprodi']);
    }

    public function render()
    {
        $user = Auth::user();
        $prodiId = $user?->managedProdi?->id;

        $pengajuanList = Pengajuanjuduls::query()
            ->with(['mahasiswa.user', 'mahasiswa.bimbingans.dosen.user', 'calonDosenPembimbing.user'])
            ->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $prodiId ?? 0))
            ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search !== '', function ($q) {
                $search = trim($this->search);
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', '%' . $search . '%')
                        ->orWhereHas('mahasiswa.user', fn($uq) => $uq->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('mahasiswa', fn($mq) => $mq->where('nim', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $dosenOptions = Dosens::query()
            ->with('user')
            ->withCount('bimbingans')
            ->orderBy('nidn')
            ->get()
            ->map(fn($d) => [
                'id'     => $d->id,
                'name'   => $d->user?->name ?? '-',
                'nidn'   => $d->nidn,
                'kuota'  => (int) ($d->kuota_bimbingan ?? 0),
                'sisa'   => max((int) ($d->kuota_bimbingan ?? 0) - (int) ($d->bimbingans_count ?? 0), 0),
            ]);

        return view('livewire.pages.kaprodi-pengajuan-judul', [
            'pengajuanList'  => $pengajuanList,
            'dosenOptions'   => $dosenOptions,
            'managedProdi'   => $user?->managedProdi,
        ]);
    }

    private function getPengajuanMilikProdi(int $pengajuanId): Pengajuanjuduls
    {
        $user = Auth::user();
        $prodiId = $user?->managedProdi?->id;

        return Pengajuanjuduls::query()
            ->whereKey($pengajuanId)
            ->whereHas('mahasiswa', fn($q) => $q->where('prodi_id', $prodiId ?? 0))
            ->firstOrFail();
    }
}
