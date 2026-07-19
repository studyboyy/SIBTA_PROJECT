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

    public array $dosen_ids = [];

    public $dosen_id = '';

    public function updatedDosenIds($value): void
    {
        $this->dosen_ids = array_values(array_unique(array_map('intval', (array) $value)));

        if (count($this->dosen_ids) > 2) {
            $this->dosen_ids = array_slice($this->dosen_ids, 0, 2);
            session()->flash('error', 'Maksimal 2 dosen pembimbing dapat dipilih.');
        }
    }

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
        if ($this->dosen_id !== '') {
            $this->dosen_ids = [(int) $this->dosen_id];
        }

        $this->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswas,id',
            'dosen_ids' => 'required|array|min:1|max:2',
            'dosen_ids.*' => 'exists:dosens,id',
        ]);

        $skipped = 0;
        $added = 0;

        foreach ($this->mahasiswa_ids as $mhsId) {
            foreach (array_values($this->dosen_ids) as $index => $dosenId) {
                // The form selects a role explicitly. Previously the first
                // selected lecturer was always treated as pembimbing_1, so
                // adding pembimbing_2 replaced the existing primary slot.
                $role = count($this->dosen_ids) > 1
                    ? ($index === 0 ? Bimbingans::PERAN_PEMBIMBING_1 : Bimbingans::PERAN_PEMBIMBING_2)
                    : $this->peran;
                $hasPrimary = $role === Bimbingans::PERAN_PEMBIMBING_1
                    || Bimbingans::query()->where('mahasiswa_id', $mhsId)->where('peran', Bimbingans::PERAN_PEMBIMBING_1)->exists();
                $existingRole = Bimbingans::query()
                    ->where('mahasiswa_id', $mhsId)
                    ->where('peran', $role)
                    ->first();
                if (! $hasPrimary || ($existingRole && (int) $existingRole->dosen_id === (int) $dosenId) || Bimbingans::query()->where('mahasiswa_id', $mhsId)->where('dosen_id', $dosenId)->exists()) {
                    $skipped++;

                    continue;
                }
                Bimbingans::setSupervisor((int) $mhsId, (int) $dosenId, $role);
                $added++;
            }
        }

        $this->reset(['mahasiswa_ids', 'dosen_ids']);

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
        if (count($this->dosen_ids) > 2) {
            $this->dosen_ids = array_slice($this->dosen_ids, 0, 2);
        }

        $mahasiswas = Mahasiswas::with(['user', 'bimbingans.dosen.user'])
            ->when($this->peran === Bimbingans::PERAN_PEMBIMBING_2, function ($query) {
                $query->whereHas('bimbingans', fn ($subQuery) => $subQuery->where('peran', Bimbingans::PERAN_PEMBIMBING_1))
                    ->whereDoesntHave('bimbingans', fn ($subQuery) => $subQuery->where('peran', Bimbingans::PERAN_PEMBIMBING_2));
            })
            ->when($this->peran === Bimbingans::PERAN_PEMBIMBING_1, function ($query) {
                $query->whereDoesntHave('bimbingans', fn ($subQuery) => $subQuery->where('peran', Bimbingans::PERAN_PEMBIMBING_2));
            })
            ->get();

        $penugasans = Mahasiswas::query()
            ->with([
                'user',
                'bimbingans' => fn ($query) => $query
                    ->with('dosen.user')
                    ->orderByRaw("CASE peran WHEN 'pembimbing_1' THEN 1 WHEN 'pembimbing_2' THEN 2 ELSE 3 END"),
            ])
            ->whereHas('bimbingans')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhere('nim', 'like', '%'.$this->search.'%')
                        ->orWhereHas('bimbingans.dosen.user', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->withMax('bimbingans', 'created_at')
            ->orderByDesc('bimbingans_max_created_at')
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
            'penugasans' => $penugasans,
            'mahasiswas' => $mahasiswas,
            'dosens' => $dosens,
            'peranOptions' => Bimbingans::peranOptions(),
        ]);
    }
}
