<?php

namespace App\Livewire\Pages;

use App\Models\Dosens;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Dosen extends Component
{
    use WithPagination, WithoutUrlPagination;
    #[Title('Data Dosen')]

    public $name, $email, $nidn, $jabatan, $phone, $kuota_bimbingan, $editId = null, $deleteId = null;
    public ?int $resetId = null;
    public ?string $resetEmail = null;
    public $selectedIds = [];
    public $selectPage = false;
    public $search = '';
    public int $perPage = 5;

    protected function dosenValidationRules(bool $isEdit = false): array
    {
        if ($isEdit) {
            $dosen = Dosens::with('user')->findOrFail($this->editId);

            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $dosen->user->id,
                'nidn' => 'required|string|max:255|unique:dosens,nidn,' . $dosen->id,
                'phone' => 'required|string|max:20|unique:dosens,phone,' . $dosen->id,
                'jabatan' => 'required|string|max:255',
                'kuota_bimbingan' => 'required|integer|min:0|max:200',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nidn' => 'required|string|max:255|unique:dosens,nidn',
            'phone' => 'required|string|max:20|unique:dosens,phone',
            'jabatan' => 'required|string|max:255',
            'kuota_bimbingan' => 'required|integer|min:0|max:200',
        ];
    }


    public function store()
    {


        if ($this->editId) {
            $this->validate($this->dosenValidationRules(true));
            $dosen = Dosens::with('user')->findOrFail($this->editId);

            $dosen->update([
                'nidn' => $this->nidn,
                'jabatan' => $this->jabatan,
                'phone' => $this->phone,
                'kuota_bimbingan' => $this->kuota_bimbingan,
            ]);

            $dosen->user->update([
                'name' => $this->name,
                'email' => $this->email
            ]);
            $this->closeModal();
            $this->dispatch('notify',  message: 'Data Berhasil Di Perbaharui');
        } else {
            $this->validate($this->dosenValidationRules());
            DB::transaction(function () {
                $path_photo = null;



                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make('Dosen123!')
                ]);

                $user->assignRole('dosen');

                $dosen = Dosens::create([
                    'user_id' => $user->id,
                    'nidn' => $this->nidn,
                    'jabatan' => $this->jabatan,
                    'phone' => $this->phone,
                    'kuota_bimbingan' => $this->kuota_bimbingan,
                    'photo' => null
                ]);

                $this->resetPage();
            });
            $this->closeModal();
            $this->dispatch('notify',  message: 'Data Berhasil Di Buat');
        }
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $dosen = Dosens::with('user')->find($id);

        $this->editId = $id;

        $this->name = $dosen->user->name;
        $this->email = $dosen->user->email;
        $this->nidn = $dosen->nidn;
        $this->jabatan = $dosen->jabatan;
        $this->phone = $dosen->phone;
        $this->kuota_bimbingan = $dosen->kuota_bimbingan;

        $this->dispatch('open-modal', name: 'dosen');
    }

    public function confirmDelete($id)
    {
        $dosen = Dosens::findOrFail($id);
        $this->name = $dosen->user->name;
        $this->deleteId = $id;
        $this->dispatch('open-modal', name: 'delete');
    }

    public function delete()
    {
        DB::transaction(function () {
            $dosen = Dosens::with('user')->findOrFail($this->deleteId);
            if ($dosen->user?->photo) {
                Storage::disk('public')->delete($dosen->user->photo);
            }
            $dosen->user->delete();
            $this->dispatch('close-modal', name: 'delete');
            $this->dispatch('notify',  message: 'Data Berhasil Di Hapus');
            $this->reset([
                'name'
            ]);
        });
    }

    public function confirmResetPassword(int $id): void
    {
        $dosen = Dosens::with('user')->findOrFail($id);

        $this->resetId = $dosen->id;
        $this->resetEmail = $dosen->user?->email;

        $this->dispatch('open-modal', name: 'reset-dosen-password');
    }

    public function resetPassword(): void
    {
        $dosen = Dosens::with('user')->findOrFail($this->resetId);

        $newPassword = 'Dosen123!';

        $dosen->user?->update([
            'password' => $newPassword,
        ]);

        $this->dispatch('close-modal', name: 'reset-dosen-password');
        $this->dispatch('notify', message: 'Password baru untuk ' . ($this->resetEmail ?? '-') . ': ' . $newPassword);

        $this->reset(['resetId', 'resetEmail']);
    }

    public function updatedSelectPage($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getDosenQuery()
                ->latest()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
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

    protected function getDosenQuery()
    {
        return Dosens::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nidn', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            });
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', message: 'Pilih data dosen terlebih dahulu.');
            return;
        }

        $ids = collect($this->selectedIds)
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        DB::transaction(function () use ($ids) {
            Dosens::with('user')
                ->whereIn('id', $ids)
                ->get()
                ->each(function ($dosen) {
                    if ($dosen->user?->photo) {
                        Storage::disk('public')->delete($dosen->user->photo);
                    }

                    $dosen->user?->delete();
                });
        });

        $this->selectedIds = [];
        $this->selectPage = false;
        $this->resetPage();
        $this->dispatch('notify', message: 'Data dosen terpilih berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->dispatch('close-modal', name: 'dosen');
        $this->reset([
            'name',
            'email',
            'nidn',
            'jabatan',
            'phone',
            'kuota_bimbingan',
            'editId',
            'deleteId'
        ]);
    }



    public function render()
    {
        $dosenList = $this->getDosenQuery()
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.dosen', [
            'dosenList' => $dosenList,
        ]);
    }
}
