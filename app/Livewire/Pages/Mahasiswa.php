<?php

namespace App\Livewire\Pages;

use App\Models\Mahasiswas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Mahasiswa extends Component
{
    use WithPagination;

    public $name;

    public $email;

    public $nim;

    public $prodi;

    public $prodi_id;

    public $angkatan;

    public $status_ta;

    public $editId = null;

    public $deleteId = null;

    public ?int $resetId = null;

    public ?string $resetEmail = null;

    public $selectedIds = [];

    public $selectPage = false;

    public $search = '';

    public int $perPage = 5;

    #[Title('Data Mahasiswa')]
    protected function mahasiswaValidationRules(bool $isEdit = false): array
    {
        if ($isEdit) {
            $mahasiswa = Mahasiswas::with('user')->findOrFail($this->editId);

            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,'.$mahasiswa->user->id,
                'nim' => 'required|string|max:30|regex:/^[0-9]+$/|unique:mahasiswas,nim,'.$mahasiswa->id,
                'prodi_id' => 'required|exists:prodis,id',
                'angkatan' => 'required|digits:4',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:30|regex:/^[0-9]+$/|unique:mahasiswas,nim',
            'email' => 'required|string|email|max:255|unique:users,email',
            'angkatan' => 'required|digits:4',
            'prodi_id' => 'required|exists:prodis,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'nim.regex' => 'NIM hanya boleh berisi angka.',
            'angkatan.digits' => 'Angkatan harus 4 digit angka, contoh: 2023.',
        ];
    }

    public function store()
    {
        if ($this->editId) {
            $this->validate($this->mahasiswaValidationRules(true));
            $mahasiswa = Mahasiswas::with('user')->findOrFail($this->editId);
            $prodi = Prodi::findOrFail($this->prodi_id);

            $mahasiswa->update([
                'nim' => $this->nim,
                'prodi' => $prodi->name,
                'prodi_id' => $prodi->id,
                'angkatan' => $this->angkatan,
            ]);
            $mahasiswa->syncStatusTa();

            $mahasiswa->user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            $this->closeModal();
            $this->dispatch('notify', message: 'Data mahasiswa berhasil diubah');

            return;
        }

        $this->validate($this->mahasiswaValidationRules());

        DB::transaction(function () {
            $prodi = Prodi::findOrFail($this->prodi_id);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make('Mahasiswa123!'),
            ]);
            $user->assignRole('mahasiswa');

            $mahasiswa = Mahasiswas::create([
                'user_id' => $user->id,
                'nim' => $this->nim,
                'angkatan' => $this->angkatan,
                'prodi' => $prodi->name,
                'prodi_id' => $prodi->id,
                'photo' => null,
                'status_ta' => Mahasiswas::STATUS_TA_PENDING,
            ]);
        });

        $this->closeModal();
        $this->dispatch('notify', message: 'Data mahasiswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $mahasiswa = Mahasiswas::with('user')->find($id);

        $this->editId = $id;
        $this->name = $mahasiswa->user->name;
        $this->email = $mahasiswa->user->email;
        $this->nim = $mahasiswa->nim;
        $this->prodi = $mahasiswa->prodi;
        $this->prodi_id = $mahasiswa->prodi_id;
        $this->angkatan = $mahasiswa->angkatan;
        $this->status_ta = $mahasiswa->status_ta;

        $this->dispatch('open-modal', name: 'mahasiswa');
    }

    public function confirmDelete($id)
    {
        $mahasiswa = Mahasiswas::findOrFail($id);
        $this->name = $mahasiswa->user->name;
        $this->deleteId = $id;
        $this->dispatch('open-modal', name: 'delete');
    }

    public function delete()
    {
        DB::transaction(function () {
            $mahasiswa = Mahasiswas::with('user')->findOrFail($this->deleteId);
            if ($mahasiswa->user?->photo) {
                Storage::disk('public')->delete($mahasiswa->user->photo);
            }
            $mahasiswa->user->delete();
            $this->dispatch('close-modal', name: 'delete');
            $this->dispatch('notify', message: 'Data Berhasil Di Hapus');
            $this->reset(['name']);
        });
    }

    public function confirmResetPassword(int $id): void
    {
        $mahasiswa = Mahasiswas::with('user')->findOrFail($id);

        $this->resetId = $mahasiswa->id;
        $this->resetEmail = $mahasiswa->user?->email;

        $this->dispatch('open-modal', name: 'reset-mahasiswa-password');
    }

    public function resetPassword(): void
    {
        $mahasiswa = Mahasiswas::with('user')->findOrFail($this->resetId);

        $newPassword = 'Mahasiswa123!';

        $mahasiswa->user?->update([
            'password' => $newPassword,
        ]);

        $this->dispatch('close-modal', name: 'reset-mahasiswa-password');
        $this->dispatch('notify', message: 'Password baru untuk '.($this->resetEmail ?? '-').': '.$newPassword);

        $this->reset(['resetId', 'resetEmail']);
    }

    public function updatedSelectPage($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getMahasiswaQuery()
                ->latest()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            return;
        }

        $this->selectedIds = [];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectPage = false;
    }

    public function updatedPerPage($value): void
    {
        $allowed = [5, 10, 15, 20];
        $this->perPage = in_array((int) $value, $allowed, true) ? (int) $value : 5;
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectPage = false;
    }

    protected function getMahasiswaQuery()
    {
        return Mahasiswas::query()
            ->with(['user', 'bimbingans', 'sidang', 'pengajuanSidang'])
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nim', 'like', "%{$search}%")
                        ->orWhere('prodi', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            });
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', message: 'Pilih data mahasiswa terlebih dahulu.');

            return;
        }

        $ids = collect($this->selectedIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        DB::transaction(function () use ($ids) {
            Mahasiswas::with('user')
                ->whereIn('id', $ids)
                ->get()
                ->each(function ($mahasiswa) {
                    if ($mahasiswa->user?->photo) {
                        Storage::disk('public')->delete($mahasiswa->user->photo);
                    }

                    $mahasiswa->user?->delete();
                });
        });

        $this->selectedIds = [];
        $this->selectPage = false;
        $this->resetPage();
        $this->dispatch('close-modal', name: 'delete-selected-mahasiswa');
        $this->dispatch('notify', message: 'Data mahasiswa terpilih berhasil dihapus.');
    }

    public function confirmDeleteSelected(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', message: 'Pilih data mahasiswa terlebih dahulu.');

            return;
        }

        $this->dispatch('open-modal', name: 'delete-selected-mahasiswa');
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['name', 'nim', 'email', 'prodi', 'prodi_id', 'angkatan', 'status_ta', 'editId']);
        $this->dispatch('close-modal', name: 'mahasiswa');
    }

    public function render()
    {
        $mahasiswaList = $this->getMahasiswaQuery()
            ->latest()
            ->paginate($this->perPage);

        $mahasiswaList->getCollection()->each->syncStatusTa();

        return view('livewire.pages.mahasiswa', [
            'mahasiswaList' => $mahasiswaList,
            'prodiOptions' => Prodi::query()->orderBy('name')->get(),
        ]);
    }
}
