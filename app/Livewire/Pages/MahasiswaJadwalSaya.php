<?php

namespace App\Livewire\Pages;

use App\Livewire\Pages\Concerns\UsesMahasiswaScope;
use App\Models\BimbinganLog;
use App\Models\Sidangs;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class MahasiswaJadwalSaya extends Component
{
    use UsesMahasiswaScope;

    public string $filter = '';

    #[Title('Jadwal Saya')]
    public function render()
    {
        $mahasiswa = $this->getMahasiswa();
        $activeDosenIds = $this->getActiveDosenIds($mahasiswa->id);

        $bimbinganList = BimbinganLog::query()
            ->with('dosen.user')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('dosen_id', $activeDosenIds)
            ->when($this->filter === 'upcoming', fn ($query) => $query->whereDate('tanggal', '>=', now()->toDateString()))
            ->when($this->filter === 'past', fn ($query) => $query->whereDate('tanggal', '<', now()->toDateString()))
            ->orderBy('tanggal')
            ->orderBy('jam')
            ->get();

        $sidang = Sidangs::query()
            ->with(['ketuaSidang.user', 'penguji1.user', 'penguji2.user'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $agenda = collect();

        if ($sidang && $this->filter !== 'past') {
            $agenda->push([
                'type' => 'sidang',
                'title' => 'Sidang Tugas Akhir',
                'date' => $sidang->jadwal,
                'time' => trim(($sidang->jam_mulai ? substr((string) $sidang->jam_mulai, 0, 5) : '').' - '.($sidang->jam_selesai ? substr((string) $sidang->jam_selesai, 0, 5) : ''), ' -'),
                'location' => $sidang->ruangan ?? 'Ruangan belum ditentukan',
                'status' => $sidang->status ?? 'pending',
                'people' => collect([
                    $sidang->ketuaSidang?->user?->name ? 'Ketua: '.$sidang->ketuaSidang->user->name : null,
                    $sidang->penguji1?->user?->name ? 'Penguji 1: '.$sidang->penguji1->user->name : null,
                    $sidang->penguji2?->user?->name ? 'Penguji 2: '.$sidang->penguji2->user->name : null,
                ])->filter()->values(),
            ]);
        }

        foreach ($bimbinganList as $log) {
            $agenda->push([
                'type' => 'bimbingan',
                'title' => 'Bimbingan '.$log->mode,
                'date' => $log->tanggal,
                'time' => $log->jam ? substr((string) $log->jam, 0, 5) : '-',
                'location' => $log->mode === 'online' ? ($log->link_online ?? '-') : ($log->lokasi ?? '-'),
                'status' => $log->status_sesi ?? 'diajukan',
                'people' => collect([$log->dosen?->user?->name])->filter()->values(),
                'confirmation' => $log->konfirmasi_mahasiswa ?? 'pending',
            ]);
        }

        return view('livewire.pages.mahasiswa-jadwal-saya', [
            'mahasiswa' => $mahasiswa,
            'agenda' => $agenda->sortBy('date')->values(),
            'nextAgenda' => $agenda
                ->filter(fn (array $item) => $item['date'] && Carbon::parse($item['date'])->startOfDay()->greaterThanOrEqualTo(now()->startOfDay()))
                ->sortBy('date')
                ->first(),
        ]);
    }
}
