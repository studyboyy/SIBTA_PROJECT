<?php

namespace App\Livewire\Pages;

use App\Models\Bimbingans;
use App\Models\Dosens;
use App\Models\Mahasiswas;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Bimbingan extends Component
{
    use WithPagination;

    #[Title('Kelola Bimbingan')]
    public array $mahasiswa_ids = [];

    public $dosen_id = '';

    public string $peran = Bimbingans::PERAN_PEMBIMBING_1;

    public $search = '';

    public int $perPage = 5;

    public ?int $deleteId = null;

    public string $deleteName = '';

    protected string $paginationTheme = 'tailwind';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 5;
        $this->resetPage();
    }

    public function simpan(): void
    {
        $this->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswas,id',
            'dosen_id' => 'required|exists:dosens,id',
            'peran' => 'required|in:'.implode(',', array_keys(Bimbingans::peranOptions())),
        ]);

        $dosen = Dosens::query()
            ->withCount('bimbingans')
            ->findOrFail((int) $this->dosen_id);

        $selectedCount = Bimbingans::query()
            ->whereIn('mahasiswa_id', $this->mahasiswa_ids)
            ->where('dosen_id', $this->dosen_id)
            ->count();
        $selectedCount = count($this->mahasiswa_ids) - $selectedCount;
        $remainingQuota = max((int) $dosen->kuota_bimbingan - (int) $dosen->bimbingans_count, 0);

        if ($remainingQuota <= 0) {
            session()->flash('error', 'Kuota dosen pembimbing ini sudah penuh.');

            return;
        }

        if ($selectedCount > $remainingQuota) {
            session()->flash('error', 'Jumlah mahasiswa dipilih melebihi sisa kuota dosen. Sisa kuota saat ini: '.$remainingQuota.'.');

            return;
        }

        $skipped = 0;
        $added = 0;

        foreach ($this->mahasiswa_ids as $mhsId) {
            $sameDosenExists = Bimbingans::query()
                ->where('mahasiswa_id', $mhsId)
                ->where('dosen_id', $this->dosen_id)
                ->exists();

            if ($sameDosenExists) {
                $skipped++;

                continue;
            }

            if ($this->peran === Bimbingans::PERAN_PEMBIMBING_2) {
                $hasPrimary = Bimbingans::query()
                    ->where('mahasiswa_id', $mhsId)
                    ->where('peran', Bimbingans::PERAN_PEMBIMBING_1)
                    ->exists();

                if (! $hasPrimary) {
                    $skipped++;

                    continue;
                }
            }

            Bimbingans::setSupervisor((int) $mhsId, (int) $this->dosen_id, $this->peran);

            $added++;
        }

        $this->reset(['mahasiswa_ids', 'dosen_id']);

        if ($added > 0 && $skipped === 0) {
            session()->flash('success', $added.' penugasan berhasil disimpan.');
        } elseif ($added > 0) {
            session()->flash('success', $added.' disimpan, '.$skipped.' dilewati (dosen sudah menjadi pembimbing mahasiswa tersebut).');
        } else {
            session()->flash('error', 'Tidak ada penugasan yang disimpan. Pastikan dosen belum menjadi pembimbing mahasiswa yang dipilih.');
        }
    }

    public function hapus(int $id): void
    {
        $bimbingan = Bimbingans::query()->findOrFail($id);
        $totalPembimbing = Bimbingans::query()
            ->where('mahasiswa_id', $bimbingan->mahasiswa_id)
            ->count();

        if ($totalPembimbing <= 1) {
            session()->flash('error', 'Pembimbing 1 wajib ada. Tambahkan pembimbing pengganti sebelum menghapus penugasan terakhir.');

            return;
        }

        if ($bimbingan->peran === Bimbingans::PERAN_PEMBIMBING_1) {
            session()->flash('error', 'Pembimbing 1 tidak dapat dihapus selama masih ada pembimbing lain. Ubah penugasan Pembimbing 1 terlebih dahulu.');

            return;
        }

        $bimbingan->delete();
        session()->flash('success', 'Penugasan berhasil dihapus.');
    }

    public function confirmHapus(int $id): void
    {
        $bimbingan = Bimbingans::query()
            ->with(['mahasiswa.user', 'dosen.user'])
            ->findOrFail($id);

        $this->deleteId = $bimbingan->id;
        $this->deleteName = ($bimbingan->mahasiswa?->user?->name ?? 'Mahasiswa').' - '.Bimbingans::peranLabel($bimbingan->peran).' - '.($bimbingan->dosen?->user?->name ?? 'Dosen');

        $this->dispatch('open-modal', name: 'delete-bimbingan');
    }

    public function hapusConfirmed(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $this->hapus($this->deleteId);

        $this->dispatch('close-modal', name: 'delete-bimbingan');
        $this->reset(['deleteId', 'deleteName']);
    }

    public function render()
    {
        $mahasiswas = Mahasiswas::with('user')
            ->when($this->peran === Bimbingans::PERAN_PEMBIMBING_2, function ($query) {
                $query->whereHas('bimbingans', fn ($subQuery) => $subQuery->where('peran', Bimbingans::PERAN_PEMBIMBING_1));
            })
            ->get();

        $bimbingans = Bimbingans::with(['mahasiswa.user', 'dosen.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('mahasiswa.user', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('mahasiswa', fn ($q) => $q->where('nim', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('dosen.user', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->orderByRaw("CASE peran WHEN 'pembimbing_1' THEN 1 WHEN 'pembimbing_2' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate($this->perPage);

        $dosens = Dosens::query()
            ->with('user')
            ->withCount('bimbingans')
            ->orderBy('nidn')
            ->get()
            ->map(function (Dosens $dosen) {
                $kuota = max((int) ($dosen->kuota_bimbingan ?? 0), 0);
                $terpakai = (int) ($dosen->bimbingans_count ?? 0);
                $sisa = max($kuota - $terpakai, 0);

                return [
                    'id' => $dosen->id,
                    'name' => $dosen->user?->name ?? 'Dosen',
                    'nidn' => $dosen->nidn,
                    'kuota' => $kuota,
                    'terpakai' => $terpakai,
                    'sisa' => $sisa,
                    'is_full' => $sisa <= 0,
                ];
            });

        return view('livewire.pages.bimbingan', [
            'bimbingans' => $bimbingans,
            'mahasiswas' => $mahasiswas,
            'dosens' => $dosens,
            'peranOptions' => Bimbingans::peranOptions(),
        ]);
    }
}
