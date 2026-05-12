<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganLog;
use App\Models\BimbinganMessage;
use App\Models\BimbinganSessionAudit;
use App\Models\Bimbingans;
use App\Models\Mahasiswas;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class DosenBimbinganLog extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Penjadwalan Bimbingan')]

    // Form fields
    public string $tanggal     = '';
    public string $jam         = '';
    public string $mode        = 'offline';
    public string $lokasi      = '';
    public string $link_online = '';
    public string $catatan     = '';
    public int|string $mahasiswa_id = '';

    // Edit mode
    public ?int $editId = null;
    // Session edit mode
    public ?string $editSessionKey = null;
    public array $editSessionAttrs = [];

    // Filter
    public string $search        = '';
    public string $filterMahasiswa = '';
    public string $filterStatusSesi = '';
    public int $perPage = 10;
    public array $catatanRevisi = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMahasiswa(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatusSesi(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    private function getDosen(): \App\Models\Dosens
    {
        $dosen = Auth::user()?->dosen;

        if (! $dosen) {
            abort(403, 'Akun ini tidak terhubung ke data dosen.');
        }

        return $dosen;
    }

    private function getMahasiswaIds(\App\Models\Dosens $dosen): \Illuminate\Support\Collection
    {
        return Bimbingans::query()
            ->where('dosen_id', $dosen->id)
            ->pluck('mahasiswa_id');
    }

    private function assertLogOwned(int $logId): BimbinganLog
    {
        $dosen = $this->getDosen();
        $log   = BimbinganLog::findOrFail($logId);

        if ((int) $log->dosen_id !== (int) $dosen->id) {
            abort(403, 'Akses ditolak.');
        }

        return $log;
    }

    private function tulisAuditStatusSesi(BimbinganLog $log, ?string $from, string $to, string $source = 'manual', ?string $note = null): void
    {
        BimbinganSessionAudit::query()->create([
            'bimbingan_log_id' => $log->id,
            'changed_by_user_id' => Auth::id(),
            'from_status_sesi' => $from,
            'to_status_sesi' => $to,
            'source' => $source,
            'note' => $note,
            'changed_at' => now(),
        ]);
    }

    public function simpan(): void
    {
        $dosen = $this->getDosen();

        $this->validate([
            'tanggal'      => 'required|date',
            'jam'          => 'nullable|date_format:H:i',
            'mode'         => 'required|in:online,offline',
            'lokasi'       => 'nullable|string|max:255',
            'link_online'  => 'nullable|url|max:255',
            'catatan'      => 'nullable|string|max:2000',
        ]);

        if ($this->mode === 'online' && trim($this->link_online) === '') {
            $this->addError('link_online', 'Link meeting wajib diisi untuk bimbingan online.');
            return;
        }

        if ($this->mode === 'offline' && trim($this->lokasi) === '') {
            $this->addError('lokasi', 'Lokasi wajib diisi untuk bimbingan offline.');
            return;
        }

        // session-level edit: update all logs that match the session attributes
        if ($this->editSessionKey) {
            $dosen = $this->getDosen();

            $query = BimbinganLog::query()
                ->where('dosen_id', $dosen->id)
                ->where('tanggal', $this->editSessionAttrs['tanggal'] ?? $this->tanggal)
                ->where('mode', $this->editSessionAttrs['mode'] ?? $this->mode);

            if (isset($this->editSessionAttrs['jam'])) {
                $query->where('jam', $this->editSessionAttrs['jam']);
            } else {
                $query->whereNull('jam');
            }

            $query->when(isset($this->editSessionAttrs['lokasi']), fn($q) => $q->where('lokasi', $this->editSessionAttrs['lokasi']));

            $logsToUpdate = $query->get();

            foreach ($logsToUpdate as $log) {
                $log->update([
                    'tanggal'      => $this->tanggal,
                    'jam'          => $this->jam !== '' ? $this->jam : null,
                    'mode'         => $this->mode,
                    'lokasi'       => $this->mode === 'offline' ? trim($this->lokasi) : null,
                    'link_online'  => $this->mode === 'online' ? trim($this->link_online) : null,
                    'catatan'      => $this->catatan,
                ]);

                $this->tulisAuditStatusSesi($log, $log->status_sesi ?? null, $log->status_sesi ?? 'diajukan', 'manual', 'Jadwal session diperbarui dosen.');
            }

            session()->flash('success', 'Penjadwalan session bimbingan berhasil diperbarui.');
            $this->resetForm();
            return;
        }

        if ($this->editId) {
            $log = $this->assertLogOwned($this->editId);
            $log->update([
                'tanggal'      => $this->tanggal,
                'jam'          => $this->jam !== '' ? $this->jam : null,
                'mode'         => $this->mode,
                'lokasi'       => $this->mode === 'offline' ? trim($this->lokasi) : null,
                'link_online'  => $this->mode === 'online' ? trim($this->link_online) : null,
                'catatan'      => $this->catatan,
            ]);
            session()->flash('success', 'Penjadwalan bimbingan berhasil diperbarui.');
        } else {
            $mahasiswaIds = $this->getMahasiswaIds($dosen);

            if ($mahasiswaIds->isEmpty()) {
                session()->flash('error', 'Tidak ada mahasiswa bimbingan yang terkait dengan akun dosen ini.');
                return;
            }

            $jumlahDibuat = 0;

            foreach ($mahasiswaIds as $mahasiswaId) {
                $log = BimbinganLog::create([
                    'mahasiswa_id' => $mahasiswaId,
                    'dosen_id'     => $dosen->id,
                    'tanggal'      => $this->tanggal,
                    'jam'          => $this->jam !== '' ? $this->jam : null,
                    'mode'         => $this->mode,
                    'lokasi'       => $this->mode === 'offline' ? trim($this->lokasi) : null,
                    'link_online'  => $this->mode === 'online' ? trim($this->link_online) : null,
                    'catatan'      => $this->catatan,
                    'status_sesi'  => 'diajukan',
                    'konfirmasi_mahasiswa' => 'pending',
                ]);

                $this->tulisAuditStatusSesi($log, null, 'diajukan', 'manual', 'Jadwal bimbingan diajukan dosen.');
                $jumlahDibuat++;
            }

            session()->flash('success', 'Jadwal bimbingan berhasil dibuat untuk ' . $jumlahDibuat . ' mahasiswa.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $log = $this->assertLogOwned($id);

        // single-log edit (backwards compatible)
        $this->editId       = $log->id;
        $this->editSessionKey = null;
        $this->editSessionAttrs = [];
        $this->mahasiswa_id = $log->mahasiswa_id;
        $this->tanggal      = $log->tanggal;
        $this->jam          = $log->jam ?? '';
        $this->mode         = $log->mode ?? 'offline';
        $this->lokasi       = $log->lokasi ?? '';
        $this->link_online  = $log->link_online ?? '';
        $this->catatan      = $log->catatan ?? '';

        $this->resetErrorBag();
    }

    public function editSession($tanggal, $jam, $mode, $lokasi, $sampleLogId = null): void
    {
        $dosen = $this->getDosen();

        $this->editSessionKey = md5(implode('|', [$tanggal ?? '', $jam ?? '', $mode ?? '', $lokasi ?? '']));
        $this->editSessionAttrs = [
            'tanggal' => $tanggal,
            'jam' => $jam,
            'mode' => $mode,
            'lokasi' => $lokasi,
        ];

        // preload form from sample log if provided
        if ($sampleLogId) {
            $log = BimbinganLog::find($sampleLogId);
            if ($log && (int) $log->dosen_id === (int) $dosen->id) {
                $this->editId = null;
                $this->mahasiswa_id = $log->mahasiswa_id;
                $this->tanggal = $log->tanggal;
                $this->jam = $log->jam ?? '';
                $this->mode = $log->mode ?? 'offline';
                $this->lokasi = $log->lokasi ?? '';
                $this->catatan = $log->catatan ?? '';
            }
        }
    }

    public function hapus(int $id): void
    {
        $log = $this->assertLogOwned($id);

        BimbinganLog::destroy($log->id);
        session()->flash('success', 'Jadwal bimbingan berhasil dihapus.');
    }

    public function ubahStatusSesi(int $id, string $statusSesi): void
    {
        if (! in_array($statusSesi, ['diajukan', 'disetujui', 'selesai', 'dibatalkan'], true)) {
            return;
        }

        $log = $this->assertLogOwned($id);

        $fromStatus = $log->status_sesi ?? 'diajukan';

        if ($fromStatus === $statusSesi) {
            return;
        }

        $log->update([
            'status_sesi' => $statusSesi,
        ]);

        $this->tulisAuditStatusSesi($log, $fromStatus, $statusSesi, 'manual', 'Status sesi diperbarui dosen.');

        session()->flash('success', 'Status sesi bimbingan berhasil diperbarui.');
    }

    public function ubahStatusSesiSession($tanggal, $jam, $mode, $lokasi, string $statusSesi): void
    {
        if (! in_array($statusSesi, ['diajukan', 'disetujui', 'selesai', 'dibatalkan'], true)) {
            return;
        }

        $dosen = $this->getDosen();
        $query = BimbinganLog::query()
            ->where('dosen_id', $dosen->id)
            ->where('tanggal', $tanggal)
            ->where('mode', $mode);

        if ($jam) {
            $query->where('jam', $jam);
        } else {
            $query->whereNull('jam');
        }

        $query->when($lokasi !== null && $lokasi !== '', fn($q) => $q->where('lokasi', $lokasi));

        $logs = $query->get();

        foreach ($logs as $log) {
            $fromStatus = $log->status_sesi ?? 'diajukan';
            if ($fromStatus === $statusSesi) {
                continue;
            }

            $log->update(['status_sesi' => $statusSesi]);

            $this->tulisAuditStatusSesi($log, $fromStatus, $statusSesi, 'manual', 'Status sesi diperbarui dosen (session).');
        }

        session()->flash('success', 'Status sesi bimbingan berhasil diperbarui untuk session.');
    }

    public function hapusSession($tanggal, $jam, $mode, $lokasi): void
    {
        $dosen = $this->getDosen();

        $query = BimbinganLog::query()
            ->where('dosen_id', $dosen->id)
            ->where('tanggal', $tanggal)
            ->where('mode', $mode);

        if ($jam) {
            $query->where('jam', $jam);
        } else {
            $query->whereNull('jam');
        }

        $query->when($lokasi !== null && $lokasi !== '', fn($q) => $q->where('lokasi', $lokasi));

        $ids = $query->pluck('id')->toArray();

        if (! empty($ids)) {
            BimbinganLog::destroy($ids);
        }

        session()->flash('success', 'Semua jadwal pada session ini telah dihapus.');
    }

    public function kirimCatatanRevisi(int $id): void
    {
        $log = $this->assertLogOwned($id);

        $this->validate([
            'catatanRevisi.' . $id => 'required|string|max:2000',
        ]);

        $message = trim((string) ($this->catatanRevisi[$id] ?? ''));

        if ($message === '') {
            $this->addError('catatanRevisi.' . $id, 'Catatan revisi tidak boleh kosong.');

            return;
        }

        BimbinganMessage::query()->create([
            'mahasiswa_id' => $log->mahasiswa_id,
            'dosen_id' => $log->dosen_id,
            'sender_role' => 'dosen',
            'message' => $message,
            'attachment' => null,
            'read_at' => null,
        ]);

        $this->catatanRevisi[$id] = '';
        $this->resetErrorBag('catatanRevisi.' . $id);

        session()->flash('success', 'Catatan revisi berhasil dikirim ke mahasiswa.');
    }

    public function resetForm(): void
    {
        $this->editId       = null;
        $this->mahasiswa_id = '';
        $this->tanggal      = '';
        $this->jam          = '';
        $this->mode         = 'offline';
        $this->lokasi       = '';
        $this->link_online  = '';
        $this->catatan      = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $dosen        = $this->getDosen();
        $mahasiswaIds = $this->getMahasiswaIds($dosen);

        $mahasiswas = Mahasiswas::query()
            ->with('user')
            ->whereIn('id', $mahasiswaIds)
            ->orderBy('nim')
            ->get();

        $logs = BimbinganLog::query()
            ->with([
                'mahasiswa.user',
                'sessionAudits.changedByUser',
                'bimbinganMessages' => function ($q) use ($dosen) {
                    $q->where('dosen_id', $dosen->id);
                },
            ])
            ->where('dosen_id', $dosen->id)
            ->when($this->filterMahasiswa !== '', fn($q) => $q->where('mahasiswa_id', $this->filterMahasiswa))
            ->when($this->filterStatusSesi !== '', fn($q) => $q->where('status_sesi', $this->filterStatusSesi))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('catatan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('mahasiswa.user', fn($qq) => $qq->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('mahasiswa', fn($qq) => $qq->where('nim', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $progressByMahasiswa = $mahasiswas->map(function ($mhs) use ($dosen) {
            $total = BimbinganLog::query()
                ->where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', $mhs->id)
                ->count('id');

            $hadir = BimbinganLog::query()
                ->where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', $mhs->id)
                ->where('konfirmasi_mahasiswa', 'hadir')
                ->count('id');

            return [
                'id' => $mhs->id,
                'name' => $mhs->user?->name ?? 'Mahasiswa',
                'nim' => $mhs->nim,
                'total' => $total,
                'hadir' => $hadir,
                'progress' => $total > 0 ? (int) round(($hadir / $total) * 100) : 0,
            ];
        })->values();

        return view('livewire.pages.dosen-bimbingan-log', [
            'mahasiswas' => $mahasiswas,
            'logs'       => $logs,
            'dosen'      => $dosen,
            'progressByMahasiswa' => $progressByMahasiswa,
        ]);
    }
}
