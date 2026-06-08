<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Pengajuanjuduls;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MahasiswaPengajuanJudul extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    #[Title('Form Pengajuan')]

    // Tab aktif: 'judul' | 'pembimbing' | 'riwayat'
    #[Url(as: 'tab')]
    public string $activeTab = 'judul';

    // --- TAB JUDUL ---
    public string $judul = '';

    public string $deskripsi = '';

    // --- TAB PEMBIMBING ---
    public string $dosenIdPengajuan = '';

    public string $alasanPengajuan = '';

    // --- RIWAYAT JUDUL (filter/search) ---
    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    // Revisi judul
    public array $editingRevisionIds = [];

    public array $revisiJudul = [];

    public array $revisiDeskripsi = [];

    public array $revisiCatatan = [];

    public function updatedActiveTab(): void
    {
        $this->resetPage();
        $this->resetErrorBag();
    }

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

    // ==========================================================
    // TAB PENGAJUAN JUDUL
    // ==========================================================

    public function saveJudul(): void
    {
        $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
        ]);

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404);
        }

        // Blokir jika sudah ada judul yang disetujui
        $approvedTitle = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['approved', 'disetujui'])
            ->exists();

        if ($approvedTitle) {
            $this->addError('judul', 'Judul Anda sudah disetujui. Form pengajuan baru tidak tersedia.');

            return;
        }

        Pengajuanjuduls::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => trim($this->judul),
            'deskripsi' => trim($this->deskripsi) ?: null,
            'status' => 'pending',
        ]);

        $this->reset(['judul', 'deskripsi']);
        $this->activeTab = 'riwayat';
        $this->dispatch('notify', message: 'Pengajuan judul berhasil dikirim.');
    }

    // ==========================================================
    // TAB PENGAJUAN DOSEN PEMBIMBING
    // ==========================================================

    public function savePengajuanPembimbing(): void
    {
        $this->validate([
            'dosenIdPengajuan' => ['required', 'exists:dosens,id'],
            'alasanPengajuan' => ['required', 'string', 'max:1000'],
        ]);

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404);
        }

        $dosenId = (int) $this->dosenIdPengajuan;

        // Blokir jika ingin mengajukan dosen yang sama dengan pembimbing aktif
        $pembimbingAktif = Bimbingans::where('mahasiswa_id', $mahasiswa->id)->first();
        if ($pembimbingAktif && (int) $pembimbingAktif->dosen_id === $dosenId) {
            $this->addError('dosenIdPengajuan', 'Dosen ini sudah menjadi pembimbing aktif Anda.');

            return;
        }

        $dosen = Dosens::query()
            ->withCount('bimbingans')
            ->findOrFail($dosenId);
        $kuotaTersisa = max((int) ($dosen->kuota_bimbingan ?? 0) - (int) ($dosen->bimbingans_count ?? 0), 0);

        if ($kuotaTersisa <= 0) {
            $this->addError('dosenIdPengajuan', 'Kuota dosen ini sudah penuh. Pilih dosen lain.');

            return;
        }

        // Blokir jika ada pengajuan pembimbing yang masih pending
        $pendingExists = PengajuanPembimbing::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            $this->addError('dosenIdPengajuan', 'Anda masih memiliki pengajuan pembimbing yang sedang diproses. Tunggu hasilnya terlebih dahulu.');

            return;
        }

        PengajuanPembimbing::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosenId,
            'alasan' => trim($this->alasanPengajuan),
            'status' => 'pending',
            'diajukan_pada' => now(),
        ]);

        $this->reset(['dosenIdPengajuan', 'alasanPengajuan']);
        $this->dispatch('notify', message: 'Pengajuan pembimbing berhasil dikirim ke kaprodi.');
    }

    // ==========================================================
    // REVISI JUDUL
    // ==========================================================

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
            fn ($id) => (int) $id !== $pengajuanId
        ));
        unset($this->revisiJudul[$pengajuanId], $this->revisiDeskripsi[$pengajuanId], $this->revisiCatatan[$pengajuanId]);
    }

    public function kirimRevisi(int $pengajuanId): void
    {
        $this->validate([
            'revisiJudul.'.$pengajuanId => ['required', 'string', 'max:255'],
            'revisiDeskripsi.'.$pengajuanId => ['nullable', 'string', 'max:3000'],
            'revisiCatatan.'.$pengajuanId => ['nullable', 'string', 'max:1500'],
        ]);

        $pengajuan = $this->getOwnedPengajuan($pengajuanId);

        if (! in_array(strtolower((string) $pengajuan->status), ['revisi', 'rejected', 'ditolak'], true)) {
            $this->addError('revisiJudul.'.$pengajuanId, 'Judul ini tidak dalam status revisi.');

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
        $this->dispatch('notify', message: 'Revisi judul berhasil dikirim.');
    }

    // ==========================================================
    // RENDER
    // ==========================================================

    public function render()
    {
        if (! in_array($this->activeTab, ['judul', 'pembimbing', 'riwayat'], true)) {
            $this->activeTab = 'judul';
        }

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404);
        }

        // Info pembimbing aktif
        $pembimbingAktif = Bimbingans::where('mahasiswa_id', $mahasiswa->id)
            ->with('dosen.user')
            ->first();

        // Pengajuan judul dengan filter/search
        $pengajuanList = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('judul', 'like', '%'.$this->search.'%')
                        ->orWhere('deskripsi', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $approvedTitle = Pengajuanjuduls::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['approved', 'disetujui'])
            ->latest('updated_at')
            ->first();

        // Riwayat pengajuan pembimbing
        $riwayatPembimbing = PengajuanPembimbing::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with('dosen.user')
            ->latest()
            ->get();

        $pendingPembimbing = $riwayatPembimbing->where('status', 'pending')->isNotEmpty();

        // Dosen options — kecuali pembimbing aktif
        $dosenOptions = Dosens::query()
            ->with('user')
            ->withCount('bimbingans')
            ->when($pembimbingAktif, fn ($q) => $q->where('id', '!=', $pembimbingAktif->dosen_id))
            ->orderBy('nidn')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->user?->name ?? '-',
                'nidn' => $d->nidn,
                'sisa' => max((int) ($d->kuota_bimbingan ?? 0) - (int) ($d->bimbingans_count ?? 0), 0),
            ]);

        return view('livewire.pages.mahasiswa-pengajuan-judul', [
            'mahasiswa' => $mahasiswa,
            'pembimbingAktif' => $pembimbingAktif,
            'approvedTitle' => $approvedTitle,
            'pengajuanList' => $pengajuanList,
            'riwayatPembimbing' => $riwayatPembimbing,
            'pendingPembimbing' => $pendingPembimbing,
            'dosenOptions' => $dosenOptions,
        ]);
    }

    private function getOwnedPengajuan(int $pengajuanId): Pengajuanjuduls
    {
        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404);
        }

        return Pengajuanjuduls::query()
            ->whereKey($pengajuanId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();
    }
}
