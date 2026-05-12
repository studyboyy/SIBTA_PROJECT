<?php

namespace App\Livewire\Pages;

use App\Models\BimbinganLog;
use App\Models\BimbinganSessionAudit;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MahasiswaBimbingan extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    #[Title('Bimbingan Saya')]
    public string $search = '';
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function konfirmasiKehadiran(int $logId, string $status): void
    {
        if (! in_array($status, ['hadir', 'tidak_hadir'], true)) {
            return;
        }

        $mahasiswa = Auth::user()?->mahasiswa;
        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $log = BimbinganLog::query()
            ->whereKey($logId)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $fromStatus = $log->status_sesi ?? 'diajukan';
        $toStatus = $status === 'hadir' ? 'disetujui' : 'dibatalkan';

        $log->update([
            'konfirmasi_mahasiswa' => $status,
            'status_sesi' => $toStatus,
        ]);

        if ($fromStatus !== $toStatus) {
            BimbinganSessionAudit::query()->create([
                'bimbingan_log_id' => $log->id,
                'changed_by_user_id' => Auth::id(),
                'from_status_sesi' => $fromStatus,
                'to_status_sesi' => $toStatus,
                'source' => 'konfirmasi-mahasiswa',
                'note' => $status === 'hadir'
                    ? 'Mahasiswa mengonfirmasi kehadiran pada sesi bimbingan.'
                    : 'Mahasiswa mengonfirmasi tidak hadir pada sesi bimbingan.',
                'changed_at' => now(),
            ]);
        }

        session()->flash('success', 'Konfirmasi kehadiran berhasil diperbarui.');
    }

    public function render()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan untuk akun ini.');
        }

        $bimbinganList = BimbinganLog::query()
            ->with(['dosen.user', 'bimbinganMessages'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('catatan', 'like', '%' . $this->search . '%')
                        ->orWhereHas('dosen.user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $totalBimbingan = BimbinganLog::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->count('id');

        $totalHadir = BimbinganLog::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('konfirmasi_mahasiswa', 'hadir')
            ->count('id');

        $progressBimbingan = $totalBimbingan > 0
            ? (int) round(($totalHadir / $totalBimbingan) * 100)
            : 0;

        return view('livewire.pages.mahasiswa-bimbingan', [
            'mahasiswa' => $mahasiswa,
            'bimbinganList' => $bimbinganList,
            'totalBimbingan' => $totalBimbingan,
            'totalHadir' => $totalHadir,
            'progressBimbingan' => $progressBimbingan,
        ]);
    }
}
