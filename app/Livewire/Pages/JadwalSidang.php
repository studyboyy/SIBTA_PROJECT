<?php

namespace App\Livewire\Pages;

use App\Models\Dosens;
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
    public string $prodi_id = '';
    public string $ketua_sidang_id = '';
    public string $penguji_1_id = '';
    public string $penguji_2_id = '';
    public ?int $editId = null;
    public string $search = '';
    public string $pengajuanStatus = '';
    public int $sidangPerPage = 15;
    public int $pengajuanPerPage = 10;

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
        $this->prodi_id = (string) ($batch->prodi_id ?? '');
        $this->ketua_sidang_id = (string) $batch->ketua_sidang_id;
        $this->penguji_1_id = (string) $batch->penguji_1_id;
        $this->penguji_2_id = (string) $batch->penguji_2_id;
    }

    public function batalEdit(): void
    {
        $this->reset(['editId', 'tanggal', 'jam_mulai', 'jam_selesai', 'ruangan', 'gelombang', 'prodi_id', 'ketua_sidang_id', 'penguji_1_id', 'penguji_2_id']);
        $this->kuotaPerGelombang = 20;
    }

    public function updatedProdiId(): void
    {
        $this->ketua_sidang_id = '';
        $this->penguji_1_id = '';
        $this->penguji_2_id = '';
    }

    public function simpan(): void
    {
        $this->validate([
            'tanggal'           => 'required|date',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'           => 'required|string|max:100',
            'gelombang'         => 'nullable|integer|min:1',
            'kuotaPerGelombang' => 'required|integer|min:1|max:500',
            'prodi_id'          => 'required|exists:prodis,id',
            'ketua_sidang_id'   => 'required|exists:dosens,id|different:penguji_1_id|different:penguji_2_id',
            'penguji_1_id'      => 'required|exists:dosens,id|different:penguji_2_id',
            'penguji_2_id'      => 'required|exists:dosens,id',
        ]);

        $wave = (int) $this->gelombang;
        if ($wave <= 0) {
            $wave = ((int) SidangBatch::query()->max('gelombang')) + 1;
        }

        $data = [
            'tanggal'         => $this->tanggal,
            'jam_mulai'       => $this->jam_mulai,
            'jam_selesai'     => $this->jam_selesai,
            'ruangan'         => $this->ruangan,
            'gelombang'       => $wave,
            'kuota'           => $this->kuotaPerGelombang,
            'prodi_id'        => $this->prodi_id,
            'ketua_sidang_id' => $this->ketua_sidang_id,
            'penguji_1_id'    => $this->penguji_1_id,
            'penguji_2_id'    => $this->penguji_2_id,
        ];

        if ($this->editId !== null) {
            SidangBatch::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Jadwal gelombang ' . $wave . ' diperbarui.');
        } else {
            SidangBatch::create($data);
            session()->flash('success', 'Jadwal gelombang ' . $wave . ' berhasil dibuat.');
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

        $targetProdiId = $pengajuan->mahasiswa?->prodi_id;

        $batches = SidangBatch::query()
            ->where('prodi_id', $targetProdiId)
            ->withCount('sidangs')
            ->orderBy('gelombang')
            ->get();

        if ($batches->isEmpty()) {
            session()->flash('error', 'Belum ada jadwal batch tersedia untuk prodi mahasiswa ini. Buat jadwal terlebih dahulu.');
            return;
        }

        $batch = $batches->first(fn($b) => $b->sidangs_count < $b->kuota);

        if (! $batch) {
            session()->flash('error', 'Semua batch sudah penuh. Tambahkan jadwal batch baru.');
            return;
        }

        DB::transaction(function () use ($pengajuan, $batch) {
            Sidangs::create([
                'mahasiswa_id'    => $pengajuan->mahasiswa_id,
                'sidang_batch_id' => $batch->id,
                'jadwal'          => $batch->tanggal,
                'jam_mulai'       => $batch->jam_mulai,
                'jam_selesai'     => $batch->jam_selesai,
                'ruangan'         => $batch->ruangan,
                'gelombang'       => $batch->gelombang,
                'ketua_sidang_id' => $batch->ketua_sidang_id,
                'penguji_1_id'    => $batch->penguji_1_id,
                'penguji_2_id'    => $batch->penguji_2_id,
            ]);

            $pengajuan->update([
                'status'              => 'approved',
                'gelombang'           => $batch->gelombang,
                'diproses_admin_pada' => now(),
            ]);
        });

        session()->flash('success', 'Disetujui & dijadwalkan ke gelombang ' . $batch->gelombang . '.');
    }

    public function rejectPengajuan(int $pengajuanId): void
    {
        $pengajuan = PengajuanSidang::findOrFail($pengajuanId);

        if ($pengajuan->status === 'approved') {
            session()->flash('error', 'Pengajuan yang sudah disetujui tidak bisa ditolak.');
            return;
        }

        $pengajuan->update([
            'status'              => 'rejected',
            'gelombang'           => null,
            'diproses_admin_pada' => now(),
        ]);

        session()->flash('success', 'Pengajuan sidang ditolak.');
    }

    public function render()
    {
        $batches = SidangBatch::query()
            ->with(['programStudi', 'ketuaSidang.user', 'penguji1.user', 'penguji2.user'])
            ->withCount('sidangs')
            ->orderBy('gelombang')
            ->get();

        $sidangs = Sidangs::query()
            ->with(['mahasiswa.user', 'ketuaSidang.user', 'penguji1.user', 'penguji2.user'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('ruangan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.user', fn($qq) => $qq->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('mahasiswa', fn($qq) => $qq->where('nim', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderBy('gelombang')
            ->orderBy('jadwal')
            ->paginate($this->sidangPerPage, ['*'], 'sidangPage');

        $pengajuanSidangs = PengajuanSidang::query()
            ->with(['mahasiswa.user', 'mahasiswa.programStudi', 'approverKaprodi'])
            ->when($this->pengajuanStatus !== '', fn($q) => $q->where('status', $this->pengajuanStatus))
            ->latest('diajukan_pada')
            ->latest('id')
            ->paginate($this->pengajuanPerPage, ['*'], 'pengajuanPage');

        $nextWave = ((int) SidangBatch::query()->max('gelombang')) + 1;

        $dosenOptions = Dosens::query()
            ->with('user')
            ->orderBy('nidn')
            ->get();

        return view('livewire.pages.jadwal-sidang', [
            'dosens'           => $dosenOptions,
            'prodiOptions'     => Prodi::query()->orderBy('name')->get(),
            'batches'          => $batches,
            'sidangs'          => $sidangs,
            'pengajuanSidangs' => $pengajuanSidangs,
            'nextWave'         => $nextWave,
        ]);
    }
}
