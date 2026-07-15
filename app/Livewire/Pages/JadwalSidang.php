<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use App\Models\PengajuanSidang;
use App\Models\Prodi;
use App\Models\SidangBatch;
use App\Models\Sidangs;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalSidang extends Component
{
    use WithPagination;

    public string $tanggal = '';

    public string $jam_mulai = '';

    public string $jam_selesai = '';

    public string $ruangan = '';

    public string $gelombang = '';

    public int $kuotaPerGelombang = 20;

    public string $ketua_sidang_id = '';

    public string $penguji_1_id = '';

    public string $penguji_2_id = '';

    public ?int $editId = null;

    public string $search = '';

    public string $pengajuanStatus = '';

    public string $mahasiswaSearch = '';

    public string $mahasiswaStatus = '';

    public string $mahasiswaProdi = '';

    public string $activeTab = 'jadwal';

    public string $confirmAction = '';

    public ?int $confirmId = null;

    public string $confirmTitle = '';

    public string $confirmMessage = '';

    public string $confirmButton = 'Ya, Lanjutkan';

    public string $confirmTone = 'rose';

    public int $sidangPerPage = 15;

    public int $pengajuanPerPage = 10;

    public int $mahasiswaPerPage = 15;

    protected string $paginationTheme = 'tailwind';

    #[Title('Jadwal Sidang')]
    public function updatedSearch(): void
    {
        $this->resetPage('sidangPage');
    }

    public function updatedPengajuanStatus(): void
    {
        $this->resetPage('pengajuanPage');
    }

    public function updatedMahasiswaSearch(): void
    {
        $this->resetPage('mahasiswaBimbinganPage');
    }

    public function updatedMahasiswaStatus(): void
    {
        $this->resetPage('mahasiswaBimbinganPage');
    }

    public function updatedMahasiswaProdi(): void
    {
        $this->resetPage('mahasiswaBimbinganPage');
    }

    public function updatedSidangPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->sidangPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 15;
        $this->resetPage('sidangPage');
    }

    public function updatedPengajuanPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->pengajuanPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage('pengajuanPage');
    }

    public function updatedMahasiswaPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->mahasiswaPerPage = in_array((int) $value, $allowed, true) ? (int) $value : 15;
        $this->resetPage('mahasiswaBimbinganPage');
    }

    public function setActiveTab(string $tab): void
    {
        if (! in_array($tab, ['jadwal', 'approval', 'sidang'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function edit(int $id): void
    {
        $batch = SidangBatch::findOrFail($id);
        $this->editId = $batch->id;
        $this->tanggal = (string) $batch->tanggal;
        $this->jam_mulai = substr((string) $batch->jam_mulai, 0, 5);
        $this->jam_selesai = substr((string) $batch->jam_selesai, 0, 5);
        $this->ruangan = (string) $batch->ruangan;
        $this->gelombang = (string) $batch->gelombang;
        $this->kuotaPerGelombang = (int) $batch->kuota;
        $this->ketua_sidang_id = (string) $batch->ketua_sidang_id;
        $this->penguji_1_id = (string) $batch->penguji_1_id;
        $this->penguji_2_id = (string) $batch->penguji_2_id;
    }

    public function batalEdit(): void
    {
        $this->reset(['editId', 'tanggal', 'jam_mulai', 'jam_selesai', 'ruangan', 'gelombang', 'ketua_sidang_id', 'penguji_1_id', 'penguji_2_id']);
        $this->kuotaPerGelombang = 20;
    }

    public function simpan(): void
    {
        $this->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'required|string|max:100',
            'gelombang' => 'nullable|integer|min:1',
            'kuotaPerGelombang' => 'required|integer|min:1|max:500',
            'ketua_sidang_id' => 'required|exists:dosens,id|different:penguji_1_id|different:penguji_2_id',
            'penguji_1_id' => 'required|exists:dosens,id|different:penguji_2_id',
            'penguji_2_id' => 'required|exists:dosens,id',
        ]);

        $wave = (int) $this->gelombang;
        if ($wave <= 0) {
            $wave = ((int) SidangBatch::query()->max('gelombang')) + 1;
        }

        $data = [
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'ruangan' => $this->ruangan,
            'gelombang' => $wave,
            'kuota' => $this->kuotaPerGelombang,
            'prodi_id' => null,
            'ketua_sidang_id' => $this->ketua_sidang_id,
            'penguji_1_id' => $this->penguji_1_id,
            'penguji_2_id' => $this->penguji_2_id,
        ];

        if ($this->editId !== null) {
            SidangBatch::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Jadwal gelombang '.$wave.' diperbarui.');
        } else {
            SidangBatch::create($data);
            session()->flash('success', 'Jadwal gelombang '.$wave.' berhasil dibuat.');
        }

        $this->batalEdit();
    }

    public function hapusBatch(int $id): void
    {
        SidangBatch::findOrFail($id)->delete();
        session()->flash('success', 'Jadwal batch dihapus.');
    }

    public function hapusSidang(int $id): void
    {
        Sidangs::findOrFail($id)->delete();
        session()->flash('success', 'Jadwal sidang mahasiswa dihapus.');
    }

    public function updateSidangStatus(int $sidangId, string $status): void
    {
        if (! in_array($status, [
            Sidangs::STATUS_PENDING,
            Sidangs::STATUS_SELESAI,
            Sidangs::STATUS_LULUS,
            Sidangs::STATUS_TIDAK_LULUS,
        ], true)) {
            return;
        }

        Sidangs::findOrFail($sidangId)->update([
            'status' => $status,
        ]);

        session()->flash('success', 'Status sidang mahasiswa berhasil diperbarui.');
    }

    public function approvePengajuan(int $pengajuanId): void
    {
        $pengajuan = PengajuanSidang::query()->with('mahasiswa')->findOrFail($pengajuanId);

        if (($pengajuan->status_kaprodi ?? 'pending') !== 'approved') {
            session()->flash('error', 'Pengajuan belum disetujui kaprodi prodi terkait.');

            return;
        }

        if ($pengajuan->status === 'approved') {
            session()->flash('error', 'Pengajuan ini sudah disetujui sebelumnya.');

            return;
        }

        if (Sidangs::query()->where('mahasiswa_id', $pengajuan->mahasiswa_id)->exists()) {
            session()->flash('error', 'Mahasiswa sudah memiliki jadwal sidang.');

            return;
        }

        $batches = SidangBatch::query()
            ->withCount('sidangs')
            ->orderBy('gelombang')
            ->get();

        if ($batches->isEmpty()) {
            session()->flash('error', 'Belum ada jadwal batch tersedia. Buat jadwal terlebih dahulu.');

            return;
        }

        $batch = $batches->first(fn ($b) => $b->sidangs_count < $b->kuota);

        if (! $batch) {
            session()->flash('error', 'Semua batch sudah penuh. Tambahkan jadwal batch baru.');

            return;
        }

        DB::transaction(function () use ($pengajuan, $batch) {
            Sidangs::create([
                'mahasiswa_id' => $pengajuan->mahasiswa_id,
                'sidang_batch_id' => $batch->id,
                'jadwal' => $batch->tanggal,
                'jam_mulai' => $batch->jam_mulai,
                'jam_selesai' => $batch->jam_selesai,
                'ruangan' => $batch->ruangan,
                'gelombang' => $batch->gelombang,
                'ketua_sidang_id' => $batch->ketua_sidang_id,
                'penguji_1_id' => $batch->penguji_1_id,
                'penguji_2_id' => $batch->penguji_2_id,
                'status' => Sidangs::STATUS_PENDING,
            ]);

            $pengajuan->update([
                'status' => 'approved',
                'gelombang' => $batch->gelombang,
                'diproses_admin_pada' => now(),
            ]);
        });

        session()->flash('success', 'Disetujui & dijadwalkan ke gelombang '.$batch->gelombang.'.');
    }

    public function rejectPengajuan(int $pengajuanId): void
    {
        $pengajuan = PengajuanSidang::findOrFail($pengajuanId);

        if ($pengajuan->status === 'approved') {
            session()->flash('error', 'Pengajuan yang sudah disetujui tidak bisa ditolak.');

            return;
        }

        $pengajuan->update([
            'status' => 'rejected',
            'gelombang' => null,
            'diproses_admin_pada' => now(),
        ]);

        session()->flash('success', 'Pengajuan sidang ditolak.');
    }

    public function openConfirm(string $action, int $id): void
    {
        $config = match ($action) {
            'hapusBatch' => [
                'title' => 'Hapus Batch Jadwal',
                'message' => 'Batch jadwal ini akan dihapus dari daftar. Pastikan tidak ada jadwal sidang penting yang masih perlu dipertahankan.',
                'button' => 'Ya, Hapus',
                'tone' => 'rose',
            ],
            'hapusSidang' => [
                'title' => 'Hapus Jadwal Sidang',
                'message' => 'Jadwal sidang mahasiswa ini akan dihapus dari batch.',
                'button' => 'Ya, Hapus',
                'tone' => 'rose',
            ],
            'approvePengajuan' => [
                'title' => 'Approve Pengajuan Sidang',
                'message' => 'Mahasiswa akan otomatis dijadwalkan ke batch yang masih memiliki kuota tersedia.',
                'button' => 'Approve & Jadwalkan',
                'tone' => 'blue',
            ],
            'rejectPengajuan' => [
                'title' => 'Tolak Pengajuan Sidang',
                'message' => 'Pengajuan sidang mahasiswa akan ditandai sebagai ditolak.',
                'button' => 'Ya, Tolak',
                'tone' => 'rose',
            ],
            default => null,
        };

        if (! $config) {
            return;
        }

        $this->confirmAction = $action;
        $this->confirmId = $id;
        $this->confirmTitle = $config['title'];
        $this->confirmMessage = $config['message'];
        $this->confirmButton = $config['button'];
        $this->confirmTone = $config['tone'];

        $this->dispatch('open-modal', name: 'confirm-jadwal-sidang');
    }

    public function runConfirm(): void
    {
        if ($this->confirmId === null) {
            return;
        }

        match ($this->confirmAction) {
            'hapusBatch' => $this->hapusBatch($this->confirmId),
            'hapusSidang' => $this->hapusSidang($this->confirmId),
            'approvePengajuan' => $this->approvePengajuan($this->confirmId),
            'rejectPengajuan' => $this->rejectPengajuan($this->confirmId),
            default => null,
        };

        $this->dispatch('close-modal', name: 'confirm-jadwal-sidang');
        $this->resetConfirm();
    }

    public function resetConfirm(): void
    {
        $this->reset([
            'confirmAction',
            'confirmId',
            'confirmTitle',
            'confirmMessage',
            'confirmButton',
            'confirmTone',
        ]);
    }

    public function render()
    {
        $batches = SidangBatch::query()
            ->with([
                'ketuaSidang.user',
                'penguji1.user',
                'penguji2.user',
                'sidangs.mahasiswa.user',
                'sidangs.mahasiswa.programStudi',
            ])
            ->withCount('sidangs')
            ->orderBy('gelombang')
            ->get();

        $pengajuanSidangs = PengajuanSidang::query()
            ->with(['mahasiswa.user', 'mahasiswa.programStudi', 'approverKaprodi'])
            ->when($this->pengajuanStatus !== '', fn ($q) => $q->where('status', $this->pengajuanStatus))
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate($this->pengajuanPerPage, ['*'], 'pengajuanPage');

        $mahasiswaBimbinganQuery = Mahasiswas::query()
            ->with(['user', 'programStudi.kaprodiUser', 'pengajuanSidang', 'bimbingans.dosen.user'])
            ->whereHas('bimbingans')
            ->when($this->mahasiswaProdi !== '', function ($query) {
                $query->where('prodi_id', (int) $this->mahasiswaProdi);
            })
            ->when($this->mahasiswaStatus === 'layak', function ($query) {
                $query->whereHas('pengajuanSidang', fn ($sidangQuery) => $sidangQuery->where('status_dosen', 'approved'));
            })
            ->when($this->mahasiswaStatus === 'belum_layak', function ($query) {
                $query->whereDoesntHave('pengajuanSidang', fn ($sidangQuery) => $sidangQuery->where('status_dosen', 'approved'));
            })
            ->when($this->mahasiswaSearch !== '', function ($query) {
                $search = trim($this->mahasiswaSearch);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhere('nim', 'like', '%'.$search.'%')
                        ->orWhere('prodi', 'like', '%'.$search.'%')
                        ->orWhereHas('programStudi', fn ($prodiQuery) => $prodiQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('programStudi.kaprodiUser', fn ($kaprodiQuery) => $kaprodiQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('bimbingans.dosen.user', fn ($dosenQuery) => $dosenQuery->where('name', 'like', '%'.$search.'%'));
                });
            });

        $hasLayakMahasiswa = (clone $mahasiswaBimbinganQuery)
            ->whereHas('pengajuanSidang', fn ($sidangQuery) => $sidangQuery->where('status_dosen', 'approved'))
            ->exists();

        $mahasiswaBimbinganList = $mahasiswaBimbinganQuery
            ->leftJoin('users as sort_users', 'sort_users.id', '=', 'mahasiswas.user_id')
            ->leftJoin('pengajuan_sidangs as sort_pengajuan_sidangs', 'sort_pengajuan_sidangs.mahasiswa_id', '=', 'mahasiswas.id')
            ->select('mahasiswas.*')
            ->when($hasLayakMahasiswa, fn ($query) => $query->orderByRaw("CASE WHEN sort_pengajuan_sidangs.status_dosen = 'approved' THEN 0 ELSE 1 END"))
            ->orderBy('sort_users.name')
            ->orderBy('mahasiswas.nim')
            ->paginate($this->mahasiswaPerPage, ['*'], 'mahasiswaBimbinganPage');

        $nextWave = ((int) SidangBatch::query()->max('gelombang')) + 1;

        $dosenOptions = Dosens::query()
            ->with('user')
            ->orderBy('nidn')
            ->get();

        $prodiOptions = Prodi::query()
            ->orderBy('name')
            ->get();

        return view('livewire.pages.jadwal-sidang', [
            'dosens' => $dosenOptions,
            'batches' => $batches,
            'pengajuanSidangs' => $pengajuanSidangs,
            'mahasiswaBimbinganList' => $mahasiswaBimbinganList,
            'prodiOptions' => $prodiOptions,
            'nextWave' => $nextWave,
        ]);
    }
}
